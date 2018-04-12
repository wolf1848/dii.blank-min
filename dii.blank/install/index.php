<?
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

use Dii\Blank\DefaultTable;

Loc::loadMessages(__FILE__);

class dii_blank extends CModule{

    public function __construct(){
        $arModuleVersion = array();

        require_once( __DIR__ . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'dii.blank';
        $this->MODULE_NAME = Loc::getMessage('DII_BLANK_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('DII_BLANK_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('DII_BLANK_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'http://bitrix';
    }

    public function doInstall(){

        ModuleManager::registerModule($this->MODULE_ID);
        $this->installDB();
        $this->InstallFiles();
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler("main","OnBuildGlobalMenu",$this->MODULE_ID,"\\Dii\\Blank\\System","addMenu");
    }

    public function doUninstall(){
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler("main","OnBuildGlobalMenu",$this->MODULE_ID,"\\Dii\\Blank\\System","addMenu");
        $this->uninstallDB();
        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function installDB(){
        if (Loader::includeModule($this->MODULE_ID)){
            DefaultTable::getEntity()->createDbTable();
        }
    }

    public function uninstallDB(){

        if (Loader::includeModule($this->MODULE_ID)){

            $connection = Application::getInstance()->getConnection();
            $connection->dropTable(DefaultTable::getTableName());
        }
    }

    public function InstallFiles(){
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/dii.blank/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
    }

    public function UnInstallFiles(){
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/local/modules/dii.blank/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
    }
}
