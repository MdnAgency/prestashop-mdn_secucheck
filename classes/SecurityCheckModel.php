<?php
class SecurityCheckModel extends ObjectModel
{
    public static $definition = array(
        'table' => 'mdn_secucheck',
        'primary' => 'id',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
            'published' => array('type' => self::TYPE_DATE, 'required' => true, 'lang' => false),
            'updated' => array('type' => self::TYPE_DATE, 'required' => true, 'lang' => false),

            // article
            'category' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'lang' => false),
            'title' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'lang' => false),
            'url' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'lang' => false),
            'content' => array('type' => self::TYPE_HTML, 'validate' => 'isString', 'required' => true, 'lang' => false),
            'summary' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'lang' => false),
            'module' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'lang' => false),

            // state
            'state' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'lang' => false),
        ),
    );

    public $id;
    public $published;
    public $updated;
    public $category;
    public $title;
    public $url;
    public $content;
    public $summary;
    public $module;
    public $state;

    public function __construct($id_primary = null, $id_lang = null)
    {
        parent::__construct($id_primary, $id_lang);
    }

    public static function createContentTable()
    {
        $sq1 = 'CREATE TABLE IF NOT EXISTS `' . self::getTableName() . '`(
            `id` int(10) unsigned NOT NULL auto_increment,
            `id_shop` int(10) unsigned NOT NULL  , 
            `title` VARCHAR(255) NOT NULL,  
            `module` VARCHAR(255) NOT NULL,   
            `category` VARCHAR(255) NOT NULL,  
            `url` VARCHAR(255) NOT NULL,  
            `summary` VARCHAR(1028) NOT NULL,  
            `content` TEXT NOT NULL,  
            `published` DATETIME NOT NULL,
            `updated` DATETIME NOT NULL,
            `state` int(1) NOT NULL, 
            PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        $result = Db::getInstance()->execute($sq1);
        return $result;
    }

    /**
     * The DB table name
     * @return string
     */
    static function getTableName() {
        return _DB_PREFIX_ . self::$definition['table'];
    }

}
