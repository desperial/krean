<?php
/**
 * Created by PhpStorm.
 * User: chiff
 * Date: 19.04.2019
 * Time: 1:12
 */

namespace console\models;

use frontend\models\Photo;
use frontend\models\Realty;
use phpQuery;
use console\models\Parser;
use Yii;
use yii\helpers\FileHelper;

class ParserInstance
{
    public $state_config; //Текущие страница/город/тип недвижимости
    private $parser; //Индивидуальные параметры парсера для сайта
    private $iterations = 5; //Кол-во попыток парсинга в случае неудачи
    private $site; //Сайт, с которого парсим
    private $page_link; //Ссылка, по которой мы будем парсить страницу со списком объектов
    private $object_link; //Ссылка, по которой мы будем парсить непосредственно объект

    private $type_text; //Тип объекта, именование для БД.
    private $page; //Спаршенная страница со списком объектов
    private $counter; //Счетчик спаршенных объектов на странице
    private $debug; //Переменная для дебага. Передается при инициации функции
    private $prev_city;
    private $prev_type;
    private $prev_page;
    private $geo_operations_count;
    private $geo_operations_limit = 5000;
    private $currs; //Сюда при необходимости получаем валюты

    private $opts = array(
        'http' => array(
            'method' => "GET",
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36\r\n"
        )
    );

    private $context;

    public $continue = false;

    public function __construct($parser, $state_config, $debug = false)
    {
        $this->parser = $parser;
        $this->debug = $debug;
        $this->state_config = $state_config;
        $this->site = $state_config->site_name;
        $this->context = stream_context_create($this->opts);
        $this->Parsing();
    }

    protected function Parsing()
    {
        $this->state_config->cur_city = $this->state_config->cur_city ?: "";
        $this->state_config->cur_type = $this->state_config->cur_type ?: "";
        if (property_exists($this->parser,"first_page") && $this->parser->first_page == 0) {
            if (!$this->state_config->cur_page && $this->state_config->cur_page !== 0)
                $this->state_config->cur_page = 0;
            else
                $this->state_config->cur_page +=1;
        }
        else
            $this->state_config->cur_page = $this->state_config->cur_page + 1 ?: 0;
        $this->parsePage();
    }

    private function parsePage()
    {
        //region Формирование ссылки на страницу со списком объектов
        if (property_exists($this->parser, "parse_link_types")) {
            if (!$this->state_config->cur_type) {
                $this->page_link = $this->parser->parse_link . $this->parser->parse_link_types[0]->link;
                $this->state_config->cur_type = $this->parser->parse_link_types[0]->link;
                $this->type_text = $this->parser->parse_link_types[0]->text;
            } else
                foreach ($this->parser->parse_link_types as $type) {
                    if ($type->link == $this->state_config->cur_type) {
                        $this->page_link = $this->parser->parse_link . $type->link;
                        $this->state_config->cur_type = $type->link;
                        $this->type_text = $type->text;
                    }
                }

        } else {
            $this->page_link = $this->parser->parse_link;
        }

        if (property_exists($this->parser, "city_addition")) {
            if (!$this->state_config->cur_city)
                $this->state_config->cur_city = $this->parser->city_addition[0];

            if (property_exists($this->parser, "city_as_subdomain"))
                $this->page_link = $this->state_config->cur_city . "." . $this->page_link;
            else {
                if (property_exists($this->parser, "link_city_first") && $this->parser->link_city_first) {
                    $this->page_link = $this->parser->parse_link . $this->state_config->cur_city . $this->state_config->cur_type;
                } else
                    if ($this->page_link)
                        $this->page_link = $this->page_link . $this->state_config->cur_city;
                    else
                        $this->page_link = $this->parser->parse_link . $this->state_config->cur_type . $this->state_config->cur_city;

            }
        }

        $check = false;
        $stop = false;
        if (property_exists($this->parser, "parse_link_suffix"))
            $this->page_link = $this->page_link . $this->parser->parse_link_suffix;
        if (property_exists($this->parser, "parse_link_prefix"))
            $this->page_link = $this->parser->parse_link_prefix . $this->page_link;
        if (property_exists($this->parser, 'cookies_file'))
            $this->page = phpQuery::newDocument(Parser::getPage($this->page_link . ($this->state_config->cur_page * $this->parser->parse_shift)));
        else
            $this->page = phpQuery::newDocumentFileHTML($this->page_link . ($this->state_config->cur_page * $this->parser->parse_shift));
        if (property_exists($this->parser, "stop_condition")) {
            if ($this->state_config->cur_page > 1 && ((pq($this->parser->stop_condition->selector)->text() == $this->parser->stop_condition->stop_value) || (!pq($this->parser->stop_condition->selector)->text()))) {
                $stop = true;
            }
        }
        //endregion

        //region Непосредственно, парсинг.
        if (!$stop) {
            if (!property_exists($this->parser, 'is_list_json') || !$this->parser->is_list_json) {
                foreach (pq($this->parser->object_selector) as $object) {
                    $check = true;
                    $object = pq($object);
                    if (!property_exists($this->parser, "object_selector_is_link") || !$this->parser->object_selector_is_link) {
                        if ($this->parser->site == "gdeetotdom.ru") {
                            $href = base64_decode($object->find($this->parser->object_link_selector)->attr("data-hide"));
                            $this->object_link = $href;
                            $this->parseObject($href);
                        }
                        else {
                            if ($object->find($this->parser->object_link_selector)->attr("href")) {
                                $this->object_link = $object->find($this->parser->object_link_selector)->attr("href");
                                $this->parseObject($object->find($this->parser->object_link_selector)->attr("href"));
                            } elseif (property_exists($this->parser, "object_link_selector2")) {
                                $this->object_link = $object->find($this->parser->object_link_selector2)->attr("href");
                                $this->parseObject($object->find($this->parser->object_link_selector2)->attr("href"));
                            }
                        }
                    } else {
                        $this->object_link = $object->attr("href");
                        $this->parseObject($this->object_link);
                    }

                }
            } else {
                if (property_exists($this->parser, 'object_selector')) {
                    $objects_list = pq($this->parser->object_selector)->text();
                    preg_match($this->parser->select_json,
                        $objects_list, $matches);
                    if (array_key_exists(1, $matches)) {
                        $objects_list = json_decode($matches[1]);
                        if ($this->parser->site == "www.domofond.ru") {
                            if (property_exists($objects_list, "itemsState"))
                                foreach ($objects_list->itemsState->items as $object) {
                                    $check = true;
                                    $this->parseObject($object->itemUrl);
                                }
                        } elseif ($this->parser->site == "mlsn.ru") {
                            if (property_exists($objects_list->search, "announcementList")) {
                                $dictionaries = $objects_list->dictionaries->entities->options;
                                foreach ($objects_list->search->announcementList as $object) {
                                    $check = true;
                                    $this->parseJson($object, $dictionaries);
                                }
                            }
                        }
                    }
                } else {
                    if ($this->parser->site == "наш.дом.рф") {
                        $objects_list = json_decode($this->page);
                        if (property_exists($this->parser, "target_type") && $this->parser->target_type == "complex") {
                            foreach ($objects_list->data->list as $object) {
                                $this->parseComplexObject($object);
                                $check = true;
                            }

                        }

                    }
                }

            }
        }
        if (!$check) {
            if (property_exists($this->parser, "city_addition")) {
                $this->switchCity();
            } elseif (property_exists($this->parser, "parse_link_types")) {
                $this->switchType();
            }
        } elseif ($check) {
            $this->sendData();
        }
        if (($this->prev_page === null || $this->prev_page < $this->state_config->cur_page) || $this->prev_type != $this->state_config->cur_type || $this->prev_city != $this->state_config->cur_city) {
            $this->continue = true;
            $this->prev_page = $this->state_config->cur_page;
        } else {
            $this->clearState();
            $this->continue = false;
        }
        //endregion
//        echo $this->page_link;
    }

    private function parseComplexObject($object)
    {
        $vars = [];
        if (property_exists($object,"complexId")) {
            $vars['site_id'] = $object->complexId;
            $vars['site_link'] = 'https://xn--80az8a.xn--d1aqf.xn--p1ai/%D1%81%D0%B5%D1%80%D0%B2%D0%B8%D1%81%D1%8B/%D0%BA%D0%B0%D1%82%D0%B0%D0%BB%D0%BE%D0%B3-%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D1%80%D0%BE%D0%B5%D0%BA/%D0%B6%D0%B8%D0%BB%D0%BE%D0%B9-%D0%BA%D0%BE%D0%BC%D0%BF%D0%BB%D0%B5%D0%BA%D1%81/' . $vars['site_id'];
            $props = phpQuery::newDocument(Parser::getPage($vars['site_link']));
            $props = json_decode(pq("script#__NEXT_DATA__")->text());
            $props = $props->props->initialState->kn->housingCommittee->current;
            foreach ($this->parser->fields as $field) {
                if (property_exists($props,$field->prop))
                    $vars[$field->field] = $props->{$field->prop};
                else
                    $vars[$field->field] = null;
            }
            $image = phpQuery::newDocument(Parser::getPage($vars['site_link']));
            $image = pq($this->parser->image_selector)->attr("src");
            if ($image != 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMzhweCIgaGVpZ2h0PSI0NnB4IiB2aWV3Qm94PSIwIDAgMzggNDYiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+CiAgICA8IS0tIEdlbmVyYXRvcjogc2tldGNodG9vbCA1NC4xICg3NjQ5MCkgLSBodHRwczovL3NrZXRjaGFwcC5jb20gLS0+CiAgICA8dGl0bGU+MjU1RTMzQzgtOTMxNC00Njg1LTk2MjktN0Q1OTQ4NjI4MkU1PC90aXRsZT4KICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBza2V0Y2h0b29sLjwvZGVzYz4KICAgIDxkZWZzPgogICAgICAgIDxwYXRoIGQ9Ik0yNCw0NCBMMjgsNDQgTDI4LDIwIEwzMiwyMCBMMzIsNiBMMTgsNiBMMTgsMTIgTDI0LDEyIEwyNCw0NCBaIE0yNCw0OCBMNiw0OCBMNiwxMiBMMTQsMTIgTDE0LDIgTDM2LDIgTDM2LDIwIEw0NCwyMCBMNDQsNDggTDI4LDQ4IEwyNCw0OCBaIE0zMiwyNCBMMzIsNDQgTDQwLDQ0IEw0MCwyNCBMMzIsMjQgWiBNMTAuMDEwMTU3MSwyMCBMMTkuOTk5OTk5NSwyMCBMMTkuOTk5OTk5NSwxNiBMMTAuMDEwMTU3MSwxNiBMMTAuMDEwMTU3MSwyMCBaIE0xMC4wMTAxNTcxLDI4IEwxOS45OTk5OTk1LDI4IEwxOS45OTk5OTk1LDI0IEwxMC4wMTAxNTcxLDI0IEwxMC4wMTAxNTcxLDI4IFogTTEwLjAxMDE1NzEsMzYgTDE5Ljk5OTk5OTUsMzYgTDE5Ljk5OTk5OTUsMzIgTDEwLjAxMDE1NzEsMzIgTDEwLjAxMDE1NzEsMzYgWiBNMTAuMDEwMTU3MSw0NCBMMTkuOTk5OTk5NSw0NCBMMTkuOTk5OTk5NSw0MCBMMTAuMDEwMTU3MSw0MCBMMTAuMDEwMTU3MSw0NCBaIiBpZD0icGF0aC0xIj48L3BhdGg+CiAgICA8L2RlZnM+CiAgICA8ZyBpZD0i0JrQsNGC0LDQu9C+0LMt0L3QvtCy0L7RgdGC0YDQvtC10LoiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogICAgICAgIDxnIGlkPSJLTl9rYXJ0LURPTS0xLjItUFJPQkxFTSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTQxMC4wMDAwMDAsIC01OTAuMDAwMDAwKSI+CiAgICAgICAgICAgIDxnIGlkPSJHcm91cC0xMy1Db3B5LTIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDExNi4wMDAwMDAsIDQ1OC4wMDAwMDApIj4KICAgICAgICAgICAgICAgIDxnIGlkPSJwaW4iIHRyYW5zZm9ybT0idHJhbnNsYXRlKDI1Ni4wMDAwMDAsIDk4LjAwMDAwMCkiPgogICAgICAgICAgICAgICAgICAgIDxnIGlkPSJHcm91cC0xNCI+CiAgICAgICAgICAgICAgICAgICAgICAgIDxnIGlkPSLirZDvuI9JY29uLS9idWlsZGluZy1waW4iIHRyYW5zZm9ybT0idHJhbnNsYXRlKDMyLjAwMDAwMCwgMzIuMDAwMDAwKSI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cmVjdCBpZD0iUmVjdGFuZ2xlIiBmaWxsLW9wYWNpdHk9IjAiIGZpbGw9IiNEOEQ4RDgiIHg9IjAiIHk9IjAiIHdpZHRoPSI0OCIgaGVpZ2h0PSI0OCI+PC9yZWN0PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPG1hc2sgaWQ9Im1hc2stMiIgZmlsbD0id2hpdGUiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDx1c2UgeGxpbms6aHJlZj0iI3BhdGgtMSI+PC91c2U+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L21hc2s+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8dXNlIGlkPSJDb21iaW5lZC1TaGFwZS1Db3B5IiBmaWxsPSIjN0E4Mzg2IiBmaWxsLXJ1bGU9Im5vbnplcm8iIHhsaW5rOmhyZWY9IiNwYXRoLTEiPjwvdXNlPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPGcgaWQ9IkHwn46oRmlsbC0vLUdyZXktZGVlcCIgbWFzaz0idXJsKCNtYXNrLTIpIiBmaWxsPSIjRkZGRkZGIiBmaWxsLXJ1bGU9Im5vbnplcm8iPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxyZWN0IGlkPSJSZWN0YW5nbGUiIHg9IjAiIHk9IjAiIHdpZHRoPSI0OCIgaGVpZ2h0PSI0OCI+PC9yZWN0PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9nPgogICAgICAgICAgICAgICAgICAgICAgICA8L2c+CiAgICAgICAgICAgICAgICAgICAgPC9nPgogICAgICAgICAgICAgICAgPC9nPgogICAgICAgICAgICA8L2c+CiAgICAgICAgPC9nPgogICAgPC9nPgo8L3N2Zz4=')
                $vars['photo'] = $image;
        }

        //region Parsing complex builder
        $builder = [];
        foreach ($this->parser->builder as $field) {
            $field_builder = explode("//", $field->prop);
            $prop_val = $object->{$this->parser->builder_object};
            foreach ($field_builder as $prop) {
                $prop_val = $prop_val->{$prop};
            }
            $builder[$field->field] = $prop_val;
        }
        //endregion

        //region Parsing complex objects
        $objects = [];

        $item = [];
        foreach ($this->parser->object as $field) {
            if (property_exists($object,$field->prop))
                $item[$field->field] = $object->{$field->prop};
            else
                $item[$field->field] = null;
        }
        $item['site'] = $this->parser->site;
        $item['site_id'] = $item['site_id'] ?: 'p-'.$item['problem_id'];
        unset($item['problem_id']);
        $item['site_link'] = 'https://xn--80az8a.xn--d1aqf.xn--p1ai/%D0%A1%D0%B5%D1%80%D0%B2%D0%B8%D1%81/%D0%BA%D0%B0%D1%82%D0%B0%D0%BB%D0%BE%D0%B3-%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D1%80%D0%BE%D0%B5%D0%BA/%D0%BE%D0%B1%D1%8A%D0%B5%D0%BA%D1%82/' . $item['site_id'];
        $props = phpQuery::newDocument(Parser::getPage($item['site_link']));
        $props = json_decode(pq("script#__NEXT_DATA__")->text());
        $props_object = explode("//", $this->parser->object_props);
        foreach ($props_object as $prop_object) {
            if (is_object($props) && property_exists($props,$prop_object))
                $props = $props->{$prop_object};
            else break;
        }

        foreach ($this->parser->object_additional as $additional_field) {
            $field_val = $props;
            $field_prop = explode("//", $additional_field->prop);
            foreach ($field_prop as $fp) {
                if (is_object($field_val) && property_exists($field_val,$fp))
                    $field_val = $field_val->{$fp};
                else
                    $field_val = null;
            }
            $item[$additional_field->field] = $field_val;
        }
        $images = [];
        if (is_object($props) && property_exists($props,$this->parser->object_images) && $props->{$this->parser->object_images}) {
            foreach ($props->{$this->parser->object_images} as $image) {
                $images[] = $image->src;
            }
        }
        $item['images'] = $images;
        $objects[] = $item;
        //endregion

        $vars['builder_id'] = $builder['site_id'];
        $vars['site'] = $this->parser->site;

        $builder['site'] = $this->parser->site;
        $builder['site_link'] = 'https://xn--80az8a.xn--d1aqf.xn--p1ai/%D0%A1%D0%B5%D1%80%D0%B2%D0%B8%D1%81/%D0%B5%D0%B4%D0%B8%D0%BD%D1%8B%D0%B9-%D1%80%D0%B5%D0%B5%D1%81%D1%82%D1%80-%D0%B7%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D1%89%D0%B8%D0%BA%D0%BE%D0%B2/%D0%B7%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D1%89%D0%B8%D0%BA/' . $builder['site_id'];

        $this->counter++;
        $this->writeComplexData($vars, $builder, $objects);
    }

    private function writeComplexData($vars, $builder_data, $objects_data)
    {
        $is_new = $is_new_builder = false;
        if (array_key_exists("site_id",$vars) && $vars['site_id']) {
            if (!$complex = Complexes::find()->where(["and", ['=', 'site', $vars['site']], ['=', 'site_id', $vars['site_id']]])->one()) {
                $complex = new Complexes();
                $is_new = true;
            }
            $photo = new ComplexesPhotos();
            foreach ($vars as $key=>$prop) {
                if ($key != "photo") {
                    $complex->{$key} = $prop;
                }
                else {
                    if ($is_new) {
                        $photo->photo = $prop;
                    }
                }
            }
        }
        if (!$builder = ComplexesBuilders::find()->where(["and", ['=', 'site', $builder_data['site']], ['=', 'site_id', $builder_data['site_id']]])->one()) {
            $builder = new ComplexesBuilders();
            $is_new_builder = true;
        }
        foreach ($builder_data as $key=>$prop) {
            $builder->{$key} = $prop;
        }
        $builder->save(false);

        if (array_key_exists("site_id",$vars) && $vars['site_id']) {
            if (!$complex->region) {
                if ($this->checkGeoRequestsLimitations())
                    $complex->getGeo(false);
                else
                    die("geo requests limit exceeded for today;");
            }
            $complex->link("builder",$builder);
            $complex->save(false);
            if ($photo->photo) {
                $photo->link("complex",$complex);
                $photo->save(false);
            }
        }

        foreach ($objects_data as $object_data) {
            $is_new_object = false;
            if (!$object = ComplexesObjects::find()->where(["and", ['=', 'site', $object_data['site']], ['=', 'site_id', $object_data['site_id']]])->one()) {
                $is_new_object = true;
                $object = new ComplexesObjects();
            }
            $photos = [];
            foreach ($object_data as $key=>$prop) {
                $photo = 0;
                if ($key != "images") {
                    $object->{$key} = strlen($prop) > 450 ? mb_substr($prop,0,450) : $prop;
                }
                else {
                    if ($is_new_object) {
                        foreach($prop as $image) {
                            $photos[] = $image;
                        }
                    }
                }
            }
            if (!$object->region) {
                if ($this->checkGeoRequestsLimitations())
                    $object->getGeo(false);
                else
                    die("geo requests limit exceeded for today;");
            }
            if (array_key_exists("site_id",$vars) && $vars['site_id']) {
                $object->link("complex", $complex);
            }
            $object->link("builder",$builder);
            $object->save(false);
            if ($is_new_object) {
                foreach ($photos as $image) {
                    $photo = new ComplexesPhotos();
                    $photo->photo = $image;
                    $photo->link("object",$object);
                    $photo->save(false);
                }
            }
        }
    }

    private function parseJson($object, $dictionaries = false)
    {
        $vars = $this->getJsonData($object, $dictionaries);

        $vars['site'] = $this->parser->site;
        $vars['deal'] = 'buy';
        $vars['photos'] = $this->getPhotos($object);
        $this->counter++;

        if ($vars['address']) {
            $this->writeData($vars);
        }
    }

    private function getJsonData($object, $dictionaries = false)
    {
        $vars = [];
        foreach ($this->parser->fields as $param) {
            if (!property_exists($param, "fixed")) {
                if (!is_array($param->prop)) {
                    if (property_exists($object, $param->prop))
                        $vars[$param->field] = $object->{$param->prop};
                } else {
                    if (array_key_exists(1, $param->prop))
                        if (property_exists($object, $param->prop[0]) && property_exists($object->{$param->prop[0]}, $param->prop[1]))
                            $vars[$param->field] = $object->{$param->prop[0]}->{$param->prop[1]};
                }
            } else
                $vars[$param->field] = $param->fixed;
            if (property_exists($param, 'dictionary')) {
                $vars[$param->field] = $dictionaries->{$param->dictionary . $vars[$param->field]}->shortName;
            }
            if (property_exists($param, "conversion")) {
                $delimiter = property_exists($param, "delimiter") ? $param->delimiter : "";
                $position = property_exists($param, "position") ? $param->position : 0;
                $match = property_exists($param, "match") ? $param->match : "";
                if (property_exists($param, "match_suffix")) {
                    if ($param->match_suffix == "object_link") {
                        $match .= preg_quote($this->object_link, "/") . "/";
                    }
                }
                $vars[$param->field] = $this->conversion($param->conversion, $vars[$param->field], $delimiter, $position, $match);
            }
        }
        return $vars;
    }


    private function parseObject($link)
    {
        $link = $this->parser->link_prefix . ($this->parser->add_site_to_object_link ? $this->parser->site : "") . $link;
        try {
            if (property_exists($this->parser, "cookies_file"))
                $object = phpQuery::newDocument(Parser::getPage($link));
            else
                $object = phpQuery::newDocumentFileHTML($link);
        } catch (Exception $e) {
            $object = null;
        }
        $vars = [];
        if (!property_exists($this->parser, 'is_list_json') || !$this->parser->is_list_json) {
            foreach ($this->parser->fields as $param) {
                $vars[$param->field] = $this->getData($param);
                if (property_exists($param, "conversion")) {
                    if (!$vars[$param->field] && property_exists($param, "alt") && $param->alt) {
                        $vars[$param->field] = $this->getAltData($param); //Alt data works with conversion only!!!
                        $delimiter = property_exists($param, "alt_delimiter") ? $param->alt_delimiter : "";
                        $position = property_exists($param, "alt_position") ? $param->alt_position : 0;
                        $match = property_exists($param, "alt_match") ? $param->alt_match : "";
                    } else {
                        $delimiter = property_exists($param, "delimiter") ? $param->delimiter : "";
                        $position = property_exists($param, "position") ? $param->position : 0;
                        $match = property_exists($param, "match") ? $param->match : "";
                        if (property_exists($param, "match_suffix")) {
                            if ($param->match_suffix == "object_link") {
                                $match .= preg_quote($this->object_link, "/") . "/";
                            }
                        }
                    }
                    $vars[$param->field] = $this->conversion($param->conversion, $vars[$param->field], $delimiter, $position, $match);
                }
            }
        } else {
            if (property_exists($this->parser, 'object_selector'))
                $object = pq($this->parser->object_selector)->text();
            preg_match($this->parser->select_json,
                $object, $matches);
            $object = json_decode($matches[1])->itemState->item;
            $vars = $this->getJsonData($object);
        }

        $vars['site'] = $this->parser->site;
        $vars['site_link'] = $link;
        $vars['deal'] = 'buy';
        $site_id = explode("/", $link);
        $site_id = $site_id[count($site_id) - 1] ? explode(".", $site_id[count($site_id) - 1]) : explode(".", $site_id[count($site_id) - 2]);
        $vars['site_id'] = $site_id[0];
        $vars['photos'] = $this->getPhotos($object);
        $this->counter++;
//        var_dump($vars['photos']);
//        var_dump($this->counter);
        if ($vars['address']) {
            $this->writeData($vars);
        }
        sleep(5);
    }

    private function switchCity()
    {
        $check = false;
        $this->prev_city = $this->state_config->cur_city;
        $this->prev_page = $this->state_config->cur_page;
        $this->state_config->cur_page = 0;
        while ($current = current($this->parser->city_addition)) {
            if ($current == $this->state_config->cur_city) {
                if ($next = next($this->parser->city_addition)) {
                    if (property_exists($this->parser, "city_as_subdomain"))
                        $this->page_link = $next . "." . $this->parser->parse_link . $this->state_config->cur_type;
                    else {
                        if (property_exists($this->parser, "link_city_first") && $this->parser->link_city_first) {
                            $this->page_link = $this->parser->parse_link . $next . $this->state_config->cur_type;
                        } else
                            $this->page_link = $this->parser->parse_link . $this->state_config->cur_type . $next;
                    }
                    $check = true;
                    $this->state_config->cur_city = $next;
                    break;
                }
            }
            $next = next($this->parser->city_addition);
        }
        reset($this->parser->city_addition);
        if ($check)
            $this->sendData();
        elseif (property_exists($this->parser, "parse_link_types")) {
            $this->switchType();
        }
    }

    private function switchType()
    {
        $check = false;
        $this->prev_type = $this->state_config->cur_type;
        $this->prev_page = $this->state_config->cur_page;
        $this->state_config->cur_page = 1;
        while ($current = current($this->parser->parse_link_types)) {
            if ($current->link == $this->state_config->cur_type && $next = next($this->parser->parse_link_types)) {
                $this->page_link = $this->parser->parse_link . $next->link;
                $this->state_config->cur_type = $next->link;
                if (property_exists($this->parser, "city_addition")) {
                    if (property_exists($this->parser, "city_as_subdomain")) {
                        $this->page_link = $this->parser->city_addition[0] . "." . $this->page_link;
                    } else {
                        if (property_exists($this->parser, "link_city_first") && $this->parser->link_city_first) {
                            $this->page_link = $this->parser->parse_link . $this->parser->city_addition[0] . $next->link;
                        } else
                            $this->page_link = $this->parser->parse_link . $next->link . $this->parser->city_addition[0];
                    }
                    $this->state_config->cur_city = $this->parser->city_addition[0];
                }
                $check = true;
                break;
            }
            $next = next($this->parser->parse_link_types);
        }
        reset($this->parser->parse_link_types);
        if ($check)
            $this->sendData();
    }

    private function sendData()
    {
        $this->saveState();
        echo '{"page":' . $this->state_config->cur_page . ',' .
            '"city":"' . $this->state_config->cur_city . '",' .
            '"type":"' . $this->state_config->cur_type . '",' .
            '"added":"' . $this->counter . '",' .
            '"site":"' . $this->parser->site . '"' .
            '"memory_usage":"' . number_format(memory_get_usage(true)) . '"' .
            "}\n";
    }

    //Parse field data
    private function getData($param)
    {
        $search_string = $param->parse_tag;
        if (property_exists($param, "parse_cond"))
            $search_string .= ":contains('" . $param->parse_cond . "')";
        if (property_exists($param, "pseudo"))
            $search_string .= $param->pseudo;
        else
            $search_string .= ":first";
        if (property_exists($param, "attribute"))
            $result = pq($search_string)->attr($param->attribute);
        elseif (property_exists($param, "next"))
            $result = pq($search_string)->next($param->next)->text();
        elseif (property_exists($param, "next_text"))
            $result = pq($search_string)->parent()->text();
        elseif (property_exists($param, "fixed"))
            $result = $param->fixed;
        else
            $result = $param->field == "description" ? pq($search_string)->html() : pq($search_string)->text();
        return $result;
    }

    //Get data for alternative parse tags (Example - no data for area in default tag, but instead it is in description)
    private function getAltData($param)
    {
        $search_string = $param->alt_parse_tag;

        if (property_exists($param, "alt_parse_cond"))
            $search_string .= ":contains('" . $param->alt_parse_cond . "')";
        if (property_exists($param, "alt_pseudo"))
            $search_string .= $param->alt_pseudo;
        else
            $search_string .= ":first";
        if (property_exists($param, "alt_attribute"))
            $result = pq($search_string)->attr($param->alt_attribute);
        elseif (property_exists($param, "alt_next"))
            $result = pq($search_string)->next($param->alt_next)->text();
        elseif (property_exists($param, "alt_next_text"))
            $result = pq($search_string)->parent()->text();
        elseif (property_exists($param, "alt_fixed"))
            $result = $param->alt_fixed;
        else
            $result = $param->field == "description" ? pq($search_string)->html() : pq($search_string)->text();
        return $result;
    }

    private function getPhotos($object)
    {
        $photos = [];
        if (!property_exists($this->parser, 'is_list_json')) {
            $items = pq($this->parser->image_selector);
            foreach ($items as $item) {
                $photo = (property_exists($this->parser, "image_link_prefix") ? $this->parser->image_link_prefix : "") . pq($item)->attr(property_exists($this->parser, 'image_src_attr') ? $this->parser->image_src_attr : "src");
                if ($photo)
                    $photos[] = $photo;
            }
        } else {
            if (property_exists($object, $this->parser->image_selector)) {
                foreach ($object->{$this->parser->image_selector}[0]->images as $image) {
                    $photos[] = $image[2]->url;
                }
            }
            elseif ($this->parser->site == "mlsn.ru") {
                if (property_exists($object,"photos")) {
                    $sizes = false;
                    $pattern = "/\{\{ size \}\}/";
                    foreach ($object->photos->sizes as $size) {
                        if ($size == "large")
                            $sizes = $size;
                    }
                    if (!$sizes)
                        $sizes = "medium";
                    foreach ($object->photos->all  as $photo) {
                        $photos[] = preg_replace($pattern,$sizes,$photo->url);
                    }
                }
            }
        }
        return $photos;
    }

    private function conversion($type, $value, $delimiter = "", $position = 0, $match = "")
    {
        foreach ($type as $conversion) {
            switch ($conversion) {
                case "spaces":
                    $value = preg_replace('/\s+/', '', $value);
                    break;
                case "inner_spaces":
                    $value = preg_replace('/\s+/', ' ', $value);
                    break;
                case "trim":
                    $value = trim($value);
                    break;
                case "intval":
                    $value = intval($value);
                    break;
                case "floatval":
                    $value = floatval($value);
                    break;
                case "currency":
                    $value = trim($value);
                    $value = mb_substr($value, -1);
                    if ($value == "€")
                        $value = "EUR";
                    elseif ($value == "$")
                        $value = "USD";
                    elseif ($value == "₽")
                        $value = "RUB";
                    break;
                case "explode":
                    $value = preg_split($delimiter, $value);
                    $value = $value[$position];
                    break;
                case 'lat':
                    preg_match("/(var lat = parseFloat\(\"?([0-9\.-]+)\"\))/",
                        $value, $matches);
                    $value = array_key_exists(2,$matches) ? $matches[2] : null;
                    break;
                case 'lon':
                    preg_match("/(var lng = parseFloat\(\"?([0-9\.-]+)\"\))/",
                        $value, $matches);
                    $value = $matches[2];
                    break;
                case 'lat_center':
                    preg_match("/center:\s*\[([\-0-9\.]*)/",
                        $value, $matches);
                    $value = $matches[1];
                    break;
                case 'lon_center':
                    preg_match("/center:\s*\[[\-0-9\.]*,\s*([\-0-9\.]*)/",
                        $value, $matches);
                    $value = $matches[1];
                    break;
                case 'match':
                    preg_match_all($match,
                        $value, $matches);
                    $value = implode("", $matches[1]);
                    break;
                case 'match_single':
                    $matches = false;
                    preg_match_all($match,
                        $value, $matches);
                    if (is_array($matches[1])) {
                        foreach ($matches[1] as $match) {
                            if ($match) {
                                $value = $match;
                                break;
                            }
                        }
                    }
                    break;
                case 'type_linked':
                    foreach ($this->parser->parse_link_types as $type) {
                        if ($type->link == $this->state_config->cur_type)
                            $value = $type->text;
                    }
                    break;
                case 'type_normalization':
                    switch ($value) {
                        case 'ОСЗ':
                        case 'Нежилое здание':
                        case "Отель":
                        case 'коммерческая недвижимость, отель (гостиница)':
                        case 'Другого и свободного назначения':
                        case 'Здания и особняки':
                            $value = 'здание';
                            break;
                        case 'Офис':
                        case 'коммерческая недвижимость, офис':
                        case 'Офисы':
                            $value = 'офис';
                            break;
                        case 'Склад':
                        case "склад/пр":
                        case 'коммерческая недвижимость, склад':
                            $value = 'склад';
                            break;
                        case 'ПП':
                        case "Производство":
                        case 'коммерческая недвижимость, производство':
                        case 'Производство и склады':
                            $value = 'производство';
                            break;
                        case 'ПСН':
                        case 'своб.назн':
                            $value = 'ПСН';
                            break;
                        case 'ТП':
                        case 'ТК':
                        case "Магазин":
                        case "торговля":
                        case 'коммерческая недвижимость, магазин':
                        case 'Торговля и сервис':
                            $value = 'торговая площадь';
                            break;
                        case 'Гостиница':
                        case 'ОТП':
                        case 'гот.бизн':
                        case 'общепит':
                        case 'СТО':
                        case 'Помещение общественного питания':
                        case 'коммерческая недвижимость, ресторан (кафе)':
                        case 'Кафе. Бары. Рестораны.':
                            $value = 'готовый бизнес';
                            break;
                        case 'Участок':
                        case 'з.участок':
                            $value = 'земельный участок';
                            break;
                        case 'Коммерческая недвижимость':
                        case 'коммерческая недвижимость':
                        case 'коммерческая недвижимость, доходный дом':
                        case 'другое':
                        case 'коммерческая недвижимость, номер в отеле':
                        case 'коммерческая недвижимость, другое':
                        case 'кварт/1эт':
                            $value = null;
                            break;
                    }
                    break;
                case "array":
                    $value = $value[$match];
                    break;
                case "mlsn_address":
                    $tmp = "";
                    if (is_object($value->region) && property_exists($value->region, "fullName"))
                        $tmp .= $value->region->fullName . ", ";
                    if (is_object($value->locality) && property_exists($value->locality, "fullName"))
                        $tmp .= $value->locality->fullName . ", ";
                    if (is_object($value->street) && property_exists($value->street, "fullName"))
                        $tmp .= $value->street->fullName . ", ";
                    if (is_object($value->house) && property_exists($value->house, "name"))
                        $tmp .= $value->house->name . ", ";
                    $value = $tmp;
                    break;
                case "mlsn_phone":
                    if (property_exists($value, "number"))
                        $value = $value->number;
                    break;
            }
        }
        return $value;
    }

    private function getPage($url)
    {
        $useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
        $timeout = 120;
        $cookies = Yii::getAlias("@console") . "/" . $this->parser->cookies_file;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com/');
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'error:' . curl_error($ch);
        } else {
            curl_close($ch);
//            print_r($content);
            return $content;
        }
        return false;
    }


    private function writeData($vars)
    {
        if ($this->debug) {
            print_r($vars);
            return false;
        }
        $is_new = false;
        if (!$object = Realty::find()
            ->where(['=', 'site', $vars['site']])
            ->andWhere(['=', 'site_id', $vars['site_id']])
            ->one()) {
            $object = new Realty();
            $is_new = true;
            $object->created_at = time();
        }
        else {
            $object->updated_at = time();
        }

        $object->lat = array_key_exists('lat', $vars) ? $vars['lat'] : 0;
        $object->lon = array_key_exists('lon', $vars) ? $vars['lon'] : 0;
        if (!$object->country) {
            if ($this->checkGeoRequestsLimitations())
                $object->getGeo(false);
            else
                die("geo requests limit exceeded for today;");
        }
//        if (array_key_exists("currency", $vars) && $vars['currency'] != "RUB") {
//            if (!$this->currs) {
//                $curr = file_get_contents("https://www.cbr-xml-daily.ru/daily_json.js");
//                $this->currs = json_decode($curr);
//            }
//            if (property_exists($this->currs->Valute, $vars['currency']))
//                $object->price = $vars['price'] * $this->currs->Valute->{$vars['currency']}->Value;
//            $object->currency = $vars['currency'];
//            $object->price_in_currency = $vars['price'];
//        } else {
//            $object->price = $vars['price'];
//        }
        $object->currency = $vars['currency'];
        $object->price = $vars['price'];
        $object->address = $vars['address'];
        $object->deal = $vars['deal'];
        $object->site = $vars['site'];
        $object->site_id = $vars['site_id'];
        $object->site_link = $vars['site_link'];
        $object->area = $vars['area'];
        $object->type = $vars['type'];
        $object->description = $vars['description'];

        $object->enabled = 1;
        if ($object->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            $is_saved = $object->save(false);
            $this->WriteImages($vars['photos'],$object->site_id,$object->id);
            try {
                $transaction->commit();
            }
            catch (\Exception $e) {
                $transaction->rollback();
                var_dump($e);
            }
        } else {
            print_r($object->errors);
        }
        unset($object);
    }

    private function saveState()
    {
        return $this->state_config->save(false);
    }

    private function clearState()
    {
        $this->state_config->delete();
    }

    private function checkGeoRequestsLimitations()
    {
        $result = false;
        if (!is_file(Yii::getAlias("@console") . "/parser_configs/geo.json")) {
            try {
                file_put_contents ( Yii::getAlias("@console") . "/parser_configs/geo.json", "{}" );
            } catch (\Exception $e) {
                die("something went terribly wrong! Try again later");
            }
        }
        if (!$geo_count = json_decode(file_get_contents(Yii::getAlias("@console") . "/parser_configs/geo.json"), false))
            $geo_count = new \stdClass();
        if (!property_exists($geo_count,"requests_done"))
            $geo_count->requests_done = 0;
        if (!property_exists($geo_count,"last_date"))
            $geo_count->last_date = date("d-m-Y");
        $cur_date = date("d-m-Y");
        if ($cur_date != $geo_count->last_date) {
            $geo_count->last_date = $cur_date;
            $geo_count->requests_done = 1;
            $result = true;
        }
        elseif ($geo_count->requests_done < $this->geo_operations_limit) {
            $geo_count->requests_done++;
            $result = true;
        }
        if ($result) {
            $geo_count = json_encode($geo_count, JSON_PRETTY_PRINT);
            file_put_contents(Yii::getAlias("@console") . "/parser_configs/geo.json", $geo_count);
        }
        return $result;
    }

    private function WriteImages($images, $site_id, $id)
    {
        $site_name = explode(".",$this->site);
        $site_name = $site_name[count($site_name)-1];
        foreach ($images as $key=>$image) {
            $img_name = Parser::getImage((property_exists($this->parser,"image_src_prefix") ? $this->parser->image_src_prefix : ""). $image,$site_name."_".$site_id."_".$key) ?: null;
            if (!$photo = Photo::getByPath($img_name)) {
                $photo = new Photo();
                $photo->realty = $id;
                $photo->link = $img_name;
                $photo->save(false);
            }
        }
    }
}