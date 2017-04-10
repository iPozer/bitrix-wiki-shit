<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class CustomComponentMenuSection extends \CBitrixComponent {

    public function onPrepareComponentParams($params) {

        $params['CACHE_TIME'] = isset($params['CACHE_TIME'])
            ? intval($params['CACHE_TIME'])
            : 3600000;


        $params["ID"] = intval($params["ID"]);
        $params["IBLOCK_ID"] = intval($params["IBLOCK_ID"]);

        $params["DEPTH_LEVEL"] = intval($params["DEPTH_LEVEL"]);
        if ($params["DEPTH_LEVEL"] <= 0) {
            $params["DEPTH_LEVEL"] = 1;
        }

        $params['FILTER_SECTION'] = [
            "IBLOCK_ID" => $params["IBLOCK_ID"],
            "GLOBAL_ACTIVE" => "Y",
            "IBLOCK_ACTIVE" => "Y",
            "<=" . "DEPTH_LEVEL" => $params["DEPTH_LEVEL"],
        ];

        $params["SEF_MODIFY"] = $params["IS_SEF"] !== "Y";

        return $params;
    }

    public function executeComponent() {
        $this->arResult["SECTIONS"] = [];
        $this->arResult["ELEMENT_LINKS"] = [];

        if ($this->startResultCache()) {
            if (!CModule::IncludeModule("iblock")) {
                $this->abortResultCache();
            }

            $arOrder = array(
                "left_margin" => "asc",
            );

            $rsSections = CIBlockSection::GetList(
                $arOrder,
                $this->arParams['FILTER_SECTION'],
                array(
                    'ELEMENT_SUBSECTIONS' => 'Y',
                    'CNT_ACTIVE' => 'Y'
                ),
                array(
                    "ID",
                    "DEPTH_LEVEL",
                    "NAME",
                    "CODE",
                    "IBLOCK_SECTION_ID"
                )
            );

            if ($this->arParams["SEF_MODIFY"])
                $rsSections->SetUrlTemplates("", $this->arParams["SECTION_URL"]);
            else
                $rsSections->SetUrlTemplates(
                    "",
                    $this->arParams["SEF_BASE_URL"] . $this->arParams["SECTION_PAGE_URL"]
                );
            while ($arSection = $rsSections->GetNext()) {
                if ($arSection['ELEMENT_CNT'] == 0 && $this->arParams['SHOW_SECTION_EMPTY'] !== 'Y') {
                    continue;
                }

                $arSection["PATH"] = '';
                foreach ($this->arResult["SECTIONS"] as $item) {
                    if ($arSection["IBLOCK_SECTION_ID"] == $item['ID']) {
                        $arSection["PATH"] = $item['~SECTION_PAGE_URL'];
                    }
                }

                $this->arResult["SECTIONS"][] = array(
                    "ID" => $arSection["ID"],
                    "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
                    "IBLOCK_SECTION_ID" => $arSection["IBLOCK_SECTION_ID"],
                    "~NAME" => $arSection["~NAME"],
                    "~SECTION_PAGE_URL" => ($arSection["DEPTH_LEVEL"] == 1)? $this->arParams["SEF_BASE_URL"].$arSection["~CODE"] . '/': $arSection["PATH"].$arSection["~CODE"] . '/',
                );
                $this->arResult["ELEMENT_LINKS"][$arSection["ID"]] = array();
            }
            $this->endResultCache();
        }

        //In "SEF" mode we'll try to parse URL and get ELEMENT_ID from it
        if ($this->arParams["IS_SEF"] === "Y") {
            $engine = new CComponentEngine($this);
            if (CModule::IncludeModule('iblock')) {
                $engine->addGreedyPart("#SECTION_CODE_PATH#");
                $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
            }
            $componentPage = $engine->guessComponentPath(
                $this->arParams["SEF_BASE_URL"],
                array(
                    "section" => $this->arParams["SECTION_PAGE_URL"],
                    "detail" => $this->arParams["DETAIL_PAGE_URL"],
                ),
                $arVariables
            );
            if ($componentPage === "detail") {
                CComponentEngine::initComponentVariables(
                    $componentPage,
                    array("SECTION_ID", "ELEMENT_ID"),
                    array(
                        "section" => array("SECTION_ID" => "SECTION_ID"),
                        "detail" => array("SECTION_ID" => "SECTION_ID", "ELEMENT_ID" => "ELEMENT_ID"),
                    ),
                    $arVariables
                );
                $this->arParams["ID"] = intval($arVariables["ELEMENT_ID"]);
            }
        }

        if (($this->arParams["ID"] > 0) && (intval($arVariables["SECTION_ID"]) <= 0) && CModule::IncludeModule("iblock")) {
            $arSelect = array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID");
            $arFilter = array(
                "ID" => $this->arParams["ID"],
                "ACTIVE" => "Y",
                "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
            );
            $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            if (($this->arParams["IS_SEF"] === "Y") && (strlen($this->arParams["DETAIL_PAGE_URL"]) > 0))
                $rsElements->SetUrlTemplates($this->arParams["SEF_BASE_URL"] . $this->arParams["DETAIL_PAGE_URL"]);
            while ($arElement = $rsElements->GetNext()) {
                $this->arResult["ELEMENT_LINKS"][$arElement["IBLOCK_SECTION_ID"]][] = $arElement["~DETAIL_PAGE_URL"];
            }
        }

        $aMenuLinksNew = array();
        $menuIndex = 0;
        $previousDepthLevel = 1;
        foreach ($this->arResult["SECTIONS"] as $arSection) {
            if ($menuIndex > 0)
                $aMenuLinksNew[$menuIndex - 1][3]["IS_PARENT"] = $arSection["DEPTH_LEVEL"] > $previousDepthLevel;
            $previousDepthLevel = $arSection["DEPTH_LEVEL"];
            $this->arResult["ELEMENT_LINKS"][$arSection["ID"]][] = urldecode($arSection["~SECTION_PAGE_URL"]);

            $aMenuLinksNew[$menuIndex++] = array(
                htmlspecialcharsbx($arSection["~NAME"]),
                $arSection["~SECTION_PAGE_URL"],
                $this->arResult["ELEMENT_LINKS"][$arSection["ID"]],
                array(
                    "SECTION_ID" => $arSection["ID"],
                    "IBLOCK_SECTION_ID" => $arSection["IBLOCK_SECTION_ID"],
                    "FROM_IBLOCK" => true,
                    "IS_PARENT" => false,
                    "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
                ),
            );
        }

        return $aMenuLinksNew;
    }

}