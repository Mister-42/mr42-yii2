<?php

namespace mr42;

class Web
{
    public function getComponents(): array
    {
        $params = (new \mister42\Params())->getValues();
        $secrets = (new \mister42\Secrets())->getValues();
        return [
            'errorHandler' => [
                'errorAction' => 'redirect/index',
            ],
            'log' => [
                'traceLevel' => YII_DEBUG ? 3 : 0,
                'targets' => [
                    [
                        'class' => 'yii\log\DbTarget',
                        'except' => ['yii\web\HttpException:404'],
                        'levels' => ['error'],
                        'logTable' => 'log_mr42_error',
                    ],
                ],
            ],
            'request' => [
                'cookieValidationKey' => $secrets['cookieValidationKey'],
            ],
            'urlManager' => [
                'baseUrl' => $params['longDomain'],
                'rules' => [
                    'sitemap.xml' => 'feed/sitemap',
                    'sitemap-articles.xml' => 'feed/sitemap-articles',
                    'sitemap-lyrics.xml' => 'feed/sitemap-lyrics',
                    'art<id:\d+>' => 'permalink/articles',
                    'dl/php<version:\d+>' => 'download/php',
                    'music/lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>.pdf' => 'music/albumpdf',
                    'music/lyrics/<artist:.*?>/<year:\d{4}>/<album:.*?>-<size:.{2,5}>.jpg' => 'music/albumcover',
                    'music/collection-cover/<id:.*>.jpg' => 'music/collection-cover',
                    'articles/<id:\d+>/<title:.*?>.pdf' => 'articles/pdf',
                ],
            ],
            'user' => [
                'class' => \Da\User\Module::class,
                'administrators' => ['admin'],
                'allowAccountDelete' => false,
                'classMap' => [
                    'Profile' => 'mister42\models\user\Profile',
                    'RegistrationForm' => 'mister42\models\user\RegistrationForm',
                    'User' => 'mister42\models\user\User',
                ],
                'controllerMap' => [
                    'profile' => ['class' => 'mister42\controllers\user\ProfileController'],
                ],
                'mailParams' => [
                    'fromEmail' => $secrets['params']['noreplyEmail'],
                ],
                'routes' => [
                    'profile/<username:\w+>' => 'profile/show',
                    'recenttracks/<username:\w+>' => 'profile/recenttracks',
                    '<action:(login|logout)>' => 'security/<action>',
                    '<action:(register|resend)>' => 'registration/<action>',
                    'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'registration/confirm',
                    'forgot' => 'recovery/request',
                    'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'recovery/reset',
                    'settings/<action:\w+>' => 'settings/<action>',
                ],
            ],
        ];
    }

    public function getValues(): array
    {
        $config['id'] = 'mr42';
        $config['basePath'] = __DIR__;
        $config['components'] = $this->getComponents();
        $config['controllerNamespace'] = 'mr42\controllers';
        return $config;
    }
}
