<?php
#components/other/controller/class.php

use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;

use \polyspirit\Bitrix\Builder\IBlock;
use \polyspirit\Bitrix\Builder\ISection;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class Controller extends \CBitrixComponent implements \Bitrix\Main\Engine\Contract\Controllerable, \Bitrix\Main\Errorable
{
    /** @var ErrorCollection */
    protected $errorCollection;

    private $page;

    public function configureActions()
    {
        //если действия не нужно конфигурировать, то пишем просто так. И будет конфиг по умолчанию 
        return [];
    }

    public function onPrepareComponentParams($arParams)
    {
        $this->errorCollection = new ErrorCollection();

        $this->arParams = $arParams;

        return $this->arParams;
    }

    protected function checkModules()
    {
        if (!Loader::includeModule('iblock')) {
            throw new SystemException(Loc::getMessage('CPS_MODULE_NOT_INSTALLED', array('#NAME#' => 'iblock')));
        }
    }

    public function executeComponent()
    {

        $this->getResult();

        try {
            $this->checkModules();
            $this->setEditButtons();
            $this->IncludeComponentTemplate($this->page);
        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }

    protected function getResult()
    {
        global $APPLICATION;

        $arParams = $this->arParams;

        if (!is_array($this->arParams['~URL_TEMPLATES'])) {
            $this->arParams['~URL_TEMPLATES'] = [];
        }

        $arUrlTemplates = [];
        foreach ($this->arParams['~URL_TEMPLATES'] as $template) {
            if (preg_match('#([a-z._-]+)=(.+)$#i', $template, $match)) {
                $arUrlTemplates[$match[1]] = $match[2];
            }
        }

        $this->page = preg_replace(
            '#-[0-9]+$#',
            '',
            CComponentEngine::ParseComponentPath(
                $this->arParams['SEF_FOLDER'],
                $arUrlTemplates,
                $arVariables
            )
        );

        $arDefaultVariableAliases404 = [];
        $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
            $arDefaultVariableAliases404,
            $this->arParams['VARIABLE_ALIASES']
        );

        $this->arResult = [
            'FOLDER' => $this->arParams['SEF_FOLDER'],
            'URL_TEMPLATES' => $arUrlTemplates,
            'VARIABLES' => $arVariables,
            'ALIASES' => $arVariableAliases,
            'PAGE' => $this->page,
        ];

        $this->arResult['IBLOCK_ID'] = $this->getTemplateName();

        $file = $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/components/other/controller/' . $this->getTemplateName() . '/result.php';

        if (file_exists($file)) {
            $arResult = $this->arResult;
            require_once $file;
            $this->arResult = $arResult;
        }
    }

    protected function setEditButtons()
    {
        global $APPLICATION;

        if (!$APPLICATION->GetShowIncludeAreas() || $this->showEditButtons === false) {
            return false;
        }

        $this->arResult['AREA_ID'] = $this->arResult['ID'] ?? $this->arResult['IBLOCK_ID'];

        $arButtons = \CIBlock::GetPanelButtons(
            $this->arResult['IBLOCK_ID'],
            $this->arResult['ID'] ?? 0,
            $this->arResult['SECTION_ID'] ?? 0,
            ['SECTION_BUTTONS' => false, 'SESSID' => false]
        );

        $APPLICATION->SetEditArea(
            $this->getEditAreaId($this->arResult['AREA_ID']),
            \CIBlock::GetComponentMenu('configure', $arButtons)
        );
    }

    /**
     * Getting array of errors.
     * @return Error[]
     */
    public function getErrors()
    {
        return $this->errorCollection->toArray();
    }

    /**
     * Getting once error with the necessary code.
     * @param string $code Code of error.
     * @return Error
     */
    public function getErrorByCode($code)
    {
        return $this->errorCollection->getErrorByCode($code);
    }
}
