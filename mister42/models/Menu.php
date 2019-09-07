<?php

namespace app\models;

use app\models\articles\ArticlesComments;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class Menu extends \yii\base\Model
{
    private $menuItems;

    public function getItemList(): array
    {
        foreach ($this->menuItems as $menuItem) {
            if (!ArrayHelper::keyExists('items', $menuItem)) {
                $menuItems[] = $menuItem;
                continue;
            }
            ArrayHelper::multisort($menuItem['items'], ['label', 'url']);
            $menuItems[] = $menuItem;
        }

        return ArrayHelper::merge($menuItems, $this->getUserMenu());
    }

    public function getUrlList($items = null): array
    {
        foreach ($items ?? self::getItemList() as $item) {
            if (!is_array($item) || ArrayHelper::keyExists('visible', $item)) {
                continue;
            }
            if (isset($item['url'])) {
                $pages[] = ArrayHelper::getValue($item, 'url.0');
            }

            if (isset($item['items'])) {
                $pages[] = self::getUrlList($item['items']);
            }
        }
        array_walk_recursive($pages, function ($val) use (&$list): void {
            $list[] = $val;
        });
        return $list;
    }

    public function init(): void
    {
        $this->menuItems = $this->getData();
        if (Yii::$app->controller->action->id === 'sitemap') {
            $this->menuItems[] = ['label' => null, 'url' => ['/user/registration/register']];
            $this->menuItems[] = ['label' => null, 'url' => ['/site/privacy']];
        }
    }

    private function getData(): array
    {
        return [
            ['label' => Yii::$app->icon->name('mask') . Html::tag('span', 'Test'), 'url' => ['/test/index'], 'visible' => $this->isAdmin()],
            ['label' => Yii::$app->icon->name('newspaper') . Html::tag('span', Yii::t('mr42', 'Articles')), 'url' => ['/articles/index'], 'visible' => true, 'active' => Yii::$app->controller->id === 'articles'],
            ['label' => Yii::$app->icon->name('calculator') . Html::tag('span', Yii::t('mr42', 'Calculator')),
                'items' => [
                    ['label' => Yii::t('mr42', 'Date (add/subtract)'), 'url' => ['/calculator/date']],
                    ['label' => Yii::t('mr42', 'Date to Date (duration)'), 'url' => ['/calculator/duration']],
                    ['label' => Yii::t('mr42', 'Microsoft® Office 365® End Date'), 'url' => ['/calculator/office365']],
                    ['label' => Yii::t('mr42', 'Time Zone Converter'), 'url' => ['/calculator/timezone']],
                    ['label' => Yii::t('mr42', 'Week Numbers'), 'url' => ['/calculator/weeknumbers']],
                    ['label' => Yii::t('mr42', 'Wifi Protected Access Pre-Shared Key'), 'url' => ['/calculator/wpapsk']],
                ],
            ],
            ['label' => Yii::$app->icon->name('tools') . Html::tag('span', Yii::t('mr42', 'Tools')),
                'items' => [
                    ['label' => Yii::t('mr42', 'Barcode Generator'), 'url' => ['/tools/barcode']],
                    ['label' => Yii::t('mr42', 'Browser Headers'), 'url' => ['/tools/headers']],
                    ['label' => Yii::t('mr42', 'Favicon Converter'), 'url' => ['/tools/favicon']],
                    ['label' => Yii::t('mr42', 'HTML to Markdown Converter'), 'url' => ['/tools/html-to-markdown']],
                    ['label' => Yii::t('mr42', 'OUI Lookup'), 'url' => ['/tools/oui']],
                    ['label' => Yii::t('mr42', 'Password Generator'), 'url' => ['/tools/password']],
                    ['label' => Yii::t('mr42', 'Phonetic Alphabet Translator'), 'url' => ['/tools/phonetic-alphabet']],
                    ['label' => Yii::t('mr42', 'QR Code Generator'), 'url' => ['/tools/qr']],
                ],
            ],
            ['label' => Yii::$app->icon->name('code') . Html::tag('span', Yii::t('mr42', 'Yii Extensions')), 'visible' => $this->isAdmin(),
                'items' => [
                    ['label' => 'Font Awesome Inline', 'url' => ['/extensions/index', 'name' => 'fontawesome-inline']],
                ],
            ],
            ['label' => Yii::$app->icon->name('music') . Html::tag('span', Yii::t('mr42', 'Music')),
                'items' => [
                    ['label' => Yii::t('mr42', 'Collection'), 'url' => ['/music/collection']],
                    ['label' => Yii::t('mr42', 'Lyrics'), 'url' => ['/music/lyrics'], 'visible' => true],
                ],
            ],
            ['label' => Yii::$app->icon->name('share-alt') . Html::tag('span', Yii::$app->name),
                'items' => [
                    ['label' => Yii::t('mr42', 'Contact'), 'url' => ['/my/contact']],
                    ['label' => Yii::t('mr42', 'My Pi'), 'url' => ['/my/pi']],
                ],
            ],
        ];
    }

    private function getUserMenu(): array
    {
        if ($this->isGuest()) {
            return [['label' => Yii::$app->icon->name('sign-in-alt') . Html::tag('span', Yii::t('usuario', 'Login')), 'url' => ['/user/security/login'], 'visible' => true]];
        }
        if ($this->isAdmin()) {
            $subMenu[] = ['label' => Yii::t('mr42', 'Create Article'), 'url' => ['/articles/create']];
            $subMenu[] = ['label' => Yii::t('usuario', 'Manage users'), 'url' => ['/user/admin/index']];
            $subMenu[] = ['label' => Yii::t('mr42', 'PHP {version}', ['version' => PHP_VERSION]), 'url' => ['/site/php']];
            $subMenu[] = '-';
        }
        $subMenu[] = ['label' => Yii::t('mr42', 'View Profile'), 'url' => ['/user/profile/show', 'username' => Yii::$app->user->identity->username]];
        $subMenu[] = '-';
        $subMenu[] = ['label' => Yii::t('usuario', 'Profile settings'), 'url' => ['/user/settings/profile']];
        $subMenu[] = ['label' => Yii::t('usuario', 'Account settings'), 'url' => ['/user/settings/account']];
        $subMenu[] = ['label' => Yii::t('usuario', 'Networks'), 'url' => ['/user/settings/networks']];
        $subMenu[] = '-';
        $subMenu[] = ['label' => Yii::t('usuario', 'Logout'), 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']];

        $unread = $this->isAdmin() ? ArticlesComments::find()->where(['not', ['active' => true]])->count() : 0;
        $unreadBadge = $unread > 0 ? Html::tag('sup', $unread, ['class' => 'badge badge-info ml-1']) : '';
        return [['label' => Yii::$app->icon->name('user-circle') . Html::tag('span', Yii::$app->user->identity->username . $unreadBadge), 'items' => $subMenu]];
    }

    private function isAdmin(): bool
    {
        return !$this->isGuest() && Yii::$app->user->identity->isAdmin;
    }

    private function isGuest(): bool
    {
        return php_sapi_name() === 'cli' || Yii::$app->controller->action->id === 'sitemap' ?: Yii::$app->user->isGuest;
    }
}
