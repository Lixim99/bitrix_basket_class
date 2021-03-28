<?php

namespace Incl\Handler;

use Bitrix\Highloadblock;
use Bitrix\Main;

class ClassHandler
{
    public function onSaleOrderSavedHandler(Main\Event $event)
    {
        $order = $event->getParameter("ENTITY");
        $discountData = $order->getDiscount()->getApplyResult();

        $arCoup = array();
        foreach ($discountData['COUPON_LIST'] as $coupId) {
            $arCoup[] = $coupId['COUPON_ID'];
        }

        if (!empty($arCoup)) {
            $promoCodeTable = Highloadblock\HighloadBlockTable::getById(PROMO_CODE)->fetch();
            $entDataClass = Highloadblock\HighloadBlockTable::compileEntity($promoCodeTable)->getDataClass();

            $entDataClass::add(
                array(
                    'UF_USER_ID' => $order->getUserId(),
                    'UF_PROMOCODE_ID' => implode(',', $arCoup)
                )
            );
        }

    }

    public static function handler($modulId, $eventType, $callback)
    {
        Main\EventManager::getInstance()->addEventHandler(
            $modulId,
            $eventType,
            array(self::class, $callback)
        );
    }
}
