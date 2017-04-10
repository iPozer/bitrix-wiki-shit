<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class CustomComponentsPhone extends \CBitrixComponent {

    public function onPrepareComponentParams($params) {
        $params['CACHE_TIME'] = isset($params['CACHE_TIME'])
            ? intval($params['CACHE_TIME'])
            : 3600000;

        return $params;
    }

    public function executeComponent() {
        if ($this->startResultCache()) {

            if (empty($this->arParams['PHONE'])) {
                $this->abortResultCache();
            }

            $this->arResult['CLEAR_PHONE'] = $this->formatPhone($this->arParams['PHONE']);
            $this->arResult['PHONE'] = $this->arParams['PHONE'];

            $this->includeComponentTemplate();
        }
    }

    public function formatPhone($phone) {
        $result = preg_replace('![^\d\+]*!', '', $phone);
        if (substr($result, 0, 1) == '8') {
            $result = '+7' . substr($result, 1);
        }

        return $result;
    }

}