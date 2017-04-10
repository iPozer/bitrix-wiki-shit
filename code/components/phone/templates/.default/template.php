<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<? if (!empty($arResult['CLEAR_PHONE'])): ?>
    <a href="tel:<?= $arResult['CLEAR_PHONE']; ?>"><?= $arResult['PHONE']; ?></a>
<? endif;  ?>