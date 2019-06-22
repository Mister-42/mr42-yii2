<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('mr42', 'Privacy Policy');
$this->params['breadcrumbs'] = [$this->title];

echo Html::tag('h1', $this->title);

echo Html::tag('p', 'At ' . Yii::$app->name . ', accessible from ' . Url::to(['site/index'], true) . ', one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by ' . Yii::$app->name . ' and how we use it.');
echo Html::tag('p', 'If you have additional questions or require more information about our Privacy Policy, do not hesitate to ' . Html::a('contact us', ['site/contact']) . '.');

echo Html::tag('h4', 'General Data Protection Regulation (GDPR)');
echo Html::tag('p', 'We are a Data Controller of your information. If you\'d like to learn more about GDPR and your rights under GDPR, please visit ' . Html::a('gdpr-info.eu', 'https://gdpr-info.eu/') . '.');
echo Html::tag('p', Yii::$app->name . ' legal basis for collecting and using the personal information described in this Privacy Policy depends on the Personal Information we collect and the specific context in which we collect the information:');
echo Html::ul([
	Yii::$app->name . ' needs to perform a contract with you',
	'You have given ' . Yii::$app->name . ' permission to do so',
	'Processing your personal information is in ' . Yii::$app->name . ' legitimate interests',
	Yii::$app->name . ' needs to comply with the law',
]);
echo Html::tag('p', Yii::$app->name . ' will retain your personal information only for as long as is necessary for the purposes set out in this Privacy Policy. We will retain and use your information to the extent necessary to comply with our legal obligations, resolve disputes, and enforce our policies.');
echo Html::tag('p', 'If you are a resident of the European Economic Area (EEA), you have certain data protection rights. If you wish to be informed what Personal Information we hold about you and if you want it to be removed from our systems, please contact us.');
echo Html::tag('p', 'In certain circumstances, you have the following data protection rights:');
echo Html::ul([
	'The right to access, update or to delete the information we have on you',
	'The right of rectification',
	'The right to object',
	'The right of restriction',
	'The right to data portability',
	'The right to withdraw consent',
]);

echo Html::tag('h4', 'Log Files');
echo Html::tag('p', Yii::$app->name . ' follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services\' analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the site, tracking users\' movement on the website, and gathering demographic information.');

echo Html::tag('h4', 'Cookies and Web Beacons');
echo Html::tag('p', 'Like any other website, ' . Yii::$app->name . ' uses \'cookies\'. These cookies are used to store information including visitors\' preferences, and the pages on the website that the visitor accessed or visited. The information is used to optimize the users\' experience by customizing our web page content based on visitors\' browser type and/or other information.');

echo Html::tag('h4', 'Children\'s Information');
echo Html::tag('p', 'Another part of our priority is adding protection for children while using the internet. We encourage parents and guardians to observe, participate in, and/or monitor and guide their online activity.');
echo Html::tag('p', Yii::$app->name . ' does not knowingly collect any Personal Identifiable Information from children under the age of 13. If you think that your child provided this kind of information on our website, we strongly encourage you to contact us immediately and we will do our best efforts to promptly remove such information from our records.');

echo Html::tag('h4', 'Online Privacy Policy Only');
echo Html::tag('p', 'Our Privacy Policy applies only to our online activities and is valid for visitors to our website with regards to the information that they shared and/or collect in ' . Yii::$app->name . '. This policy is not applicable to any information collected offline or via channels other than this website.');

echo Html::tag('h4', 'Consent');
echo Html::tag('p', 'By using our website, you hereby consent to our Privacy Policy and agree to its terms.');
