<?php
require_once _PS_MODULE_DIR_ . '/mdn_secucheck/classes/SecurityCheckModel.php';

class Mdn_Secucheck extends Module
{
    public function __construct()
    {
        $this->name = 'mdn_secucheck';
        $this->tab = 'seo';
        $this->version = '1.0.0';
        $this->author = 'Loris Pinna';
        $this->author_uri = 'https://lorispinna.com/';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->displayName = $this->trans('MDN Security Check', array(), 'Modules.Mdnblog.Mdnblog');
        $this->description = $this->trans('Check module security issue, based on https://security.friendsofpresta.org/feed.xml', array(), 'Modules.Mdnblog.Mdnblog');
        $this->ps_versions_compliancy = array(
            'min' => '1.7.1.0',
            'max' => _PS_VERSION_,
        );
        parent::__construct();
    }

    public function install()
    {
        return SecurityCheckModel::createContentTable() && parent::install(); // TODO: Change the autogenerated stub
    }

    public function getContent()
    {
        $installedModules = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."module`");

        if (Tools::getValue("check_security") && (Tools::getValue("entry"))) {
            $model = new SecurityCheckModel((int) Tools::getValue("entry"));
            $model->state = 1;
            $model->save();
        }

        $this->saveFeed();

        $Collection = new PrestaShopCollection("SecurityCheckModel");
        $Collection->orderBy("published", "DESC");

        /*
         * MODULES ENTRIES
         */
        $entries = array_map(
            function ($v) use($installedModules) {
                $installed = array_values(array_filter($installedModules, function ($module) use ($v) {
                    return $module['name'] == $v->module;
                }));
                return [
                    'id' => $v->id,
                    'type' => $v->category,
                    'module' => $v->module,
                    'title' => $v->title,
                    'summary' => $v->summary,
                    'state' => count($installed) >= 1 ? $v->state : 2,
                    'published' => $v->published,
                    'url' => $v->url,
                    'is_installed' => count($installed) >= 1,
                    'module_version' => count($installed) >= 1 ? $installed[0]['version']: "-"
                ];
            }, $Collection->where("category", "in", ["modules", "module"])->getAll()->getResults()
        );

        usort($entries, function ($a, $b) {
            return $a['state'] <=> $b['state'];
        });

        /*
         * OTHER ENTRIES
         * - When Other Entries we could not check if modules are installed now
         */
        $Collection = new PrestaShopCollection("SecurityCheckModel");
        $Collection->orderBy("published", "DESC");
        $other_entries = array_map(
            function ($v) use($installedModules) {
                return [
                    'id' => $v->id,
                    'type' => $v->category,
                    'module' => $v->module,
                    'title' => $v->title,
                    'summary' => $v->summary,
                    'state' => $v->state,
                    'published' => $v->published,
                    'url' => $v->url
                ];
            }, $Collection->where("category", "!=", ["modules", "module"])->getAll()->getResults()
        );

        usort($other_entries, function ($a, $b) {
            return $a['state'] <=> $b['state'];
        });

        $this->smarty->assign([
            'modules_entries' => $entries,
            'other_entries' => $other_entries
        ]);
        return $this->display(__DIR__, "views/admin.tpl");
    }

    /**
     * Save an entry into DB
     * @param $entry
     * @return void
     */
    private function saveEntryFeed($entry) {
        if(Db::getInstance()->getValue("SELECT 'x' FROM `".SecurityCheckModel::getTableName()."` WHERE url = '".$entry['id']."'") === false) {

            $url_explode = explode("/", $entry['id']);
            $module = $url_explode[count($url_explode) - 1];

            if(count($entry['category']) > 1) {
                $category = implode(",", array_map(function ($v) {
                    return $v['@attributes']['term'];
                }, $entry['category']));
            }
            else {
                $category = $entry['category']['@attributes']['term'];
            }

            $check = new SecurityCheckModel();
            $check->url = $entry['id'];
            $check->content = $entry['content'];
            $check->title = $entry['title'];
            $check->updated = $entry['updated'];
            $check->published = $entry['published'];
            $check->summary = $entry['summary'];
            $check->category = $category;
            $check->state = 0;
            $check->module = $module;
            try {
                $saved = $check->save();
            } catch (PrestaShopException $e) {
                dump($e);
            } catch (Exception $e) {
                dump($e);
            }
        }
    }

    /**
     * Save feed into DB
     * @return void
     */
    private  function saveFeed() {
        if($this->hasCheckRecently())
            return;

        $feed = $this->loadFeed();
        foreach ($feed as $entry) {
            $this->saveEntryFeed($entry);
        }
        Configuration::set("MDN_SECUCHECK_LAST", time());
    }

    /**
     * Load RSS XML Reed
     * @return mixed
     */
    private function loadFeed() {
        $feed = implode(file('https://security.friendsofpresta.org/feed.xml'));
        $xml = simplexml_load_string($feed);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        return $array['entry'];
    }

    /**
     * Check if a check has been done recently
     * @return bool
     */
    public function hasCheckRecently() {
        $last = Configuration::get("MDN_SECUCHECK_LAST");
        if($last === false)
            return false;

        return ($last > strtolower("today midnight"));

    }
}