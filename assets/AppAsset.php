<?php
namespace app\assets;
use Yii;
use yii\web\AssetBundle;
use yii\web\View;

class AppAsset extends AssetBundle
{
	public $sourcePath = '@app/static/css';

	public $css = [
		'site.less',
	];

	public $js = [
	];

	public $depends = [
 		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapAsset',
	];

	public function init()
	{
		Yii::$app->view->registerJs("btn=$('#btn-scrolltop');tp=$('html,body').offset().top+150;$(document).on('scroll',function(){pos=$(window).scrollTop();if(pos>tp&&!btn.is(':visible')){btn.fadeIn();}else if(pos<tp&&btn.is(':visible')){btn.fadeOut();}}).scroll();btn.on('click',function(e){\$('html,body').animate({scrollTop:0},1000);history.pushState({},\"\",$(\"link[rel='canonical']\").attr('href'));});", View::POS_READY);
		Yii::$app->view->registerJs("$('a').each(function(){var a=new RegExp('/'+window.location.host+'/');if(!a.test(this.href)){\$(this).attr('target','_blank')}});", View::POS_READY);
		Yii::$app->view->registerJs("$(function(){\$('[data-toggle=\"tooltip\"]').tooltip()});", View::POS_READY);
	}

	public $publishOptions = [
		'forceCopy' => YII_ENV_DEV,
	];
}
