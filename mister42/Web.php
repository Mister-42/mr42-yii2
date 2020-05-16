<?php

namespace mister42;

class Web
{
    private array $secrets;

    public function __construct()
    {
        $this->secrets = (new Secrets())->getValues();
    }

    public function getValues(): array
    {
        $config['id'] = 'mister42';
        //$config['catchAll'] = in_array($_SERVER['REMOTE_ADDR'], $this->secrets['params']['specialIPs']) ? null : ['site/offline'];
        $config['basePath'] = __DIR__;
        $config['components'] = $this->getComponents();
        $config['modules'] = $this->getModules();
        $config['params'] = (new Params())->getValues();
        $config['controllerNamespace'] = 'mister42\controllers';

        if (YII_DEBUG && php_sapi_name() !== 'cli') {
            $config['bootstrap'] = ['debug'];
            $config['modules']['debug'] = [
                'class' => 'yii\debug\Module',
                'allowedIPs' => $this->secrets['params']['specialIPs'],
            ];
        }

        return $config;
    }

    private function getComponents(): array
    {
        return [
            'authClientCollection' => [
                'class' => \yii\authclient\Collection::class,
                'clients' => [
                    'facebook' => [
                        'class' => 'Da\User\AuthClient\Facebook',
                        'clientId' => $this->secrets['facebook']['Id'],
                        'clientSecret' => $this->secrets['facebook']['Secret'],
                    ],
                    'github' => [
                        'class' => 'Da\User\AuthClient\GitHub',
                        'clientId' => $this->secrets['github']['Id'],
                        'clientSecret' => $this->secrets['github']['Secret'],
                    ],
                    'google' => [
                        'class' => 'Da\User\AuthClient\Google',
                        'clientId' => $this->secrets['google']['Id'],
                        'clientSecret' => $this->secrets['google']['Secret'],
                    ],
                ],
            ],
            'errorHandler' => [
                'errorAction' => 'site/error',
            ],
            'log' => [
                'traceLevel' => YII_DEBUG ? 3 : 0,
                'targets' => [
                    [
                        'class' => 'yii\log\DbTarget',
                        'except' => ['yii\web\HttpException:404'],
                        'levels' => ['error'],
                        'logTable' => 'log_mister42_error',
                    ],
                ],
            ],
            'recaptcha' => [
                'class' => 'Da\User\Component\ReCaptchaComponent',
                'key' => $this->secrets['google']['reCAPTCHA']['key'],
                'secret' => $this->secrets['google']['reCAPTCHA']['secret'],
            ],
            'request' => [
                'cookieValidationKey' => $this->secrets['cookieValidationKey'],
            ],
            'session' => [
                'class' => 'yii\web\DbSession',
                'sessionTable' => 'x_session',
            ],
            'view' => [
                'theme' => [
                    'pathMap' => [
                        '@Da/User/resources/views' => '@mister42/views/user',
                    ],
                ],
            ],
        ];
    }

    private function getModules(): array
    {
        return [
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
                    'fromEmail' => $this->secrets['params']['noreplyEmail'],
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
}
