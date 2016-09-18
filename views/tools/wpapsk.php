<?php
use app\assets\ClipboardJsAsset;
use yii\base\DynamicModel;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

$this->title = 'WPA PSK Calculator';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

ClipboardJsAsset::register($this);
$this->registerJs('function print_psk(a){$(".btn").removeClass("disabled");$(".clipboard-js-init").removeClass("hidden");$("#psk").attr("class","well well-sm alert-success text-center").html(a).parent().removeClass("col-md-12").addClass("col-md-11")}function status(a){$(".progress-bar").width(Math.round(a)+"%")}function cal_psk(){var c=$("#ssid").val(),b=$("#pass").val();if(!b||!c){return $("#psk").attr("class","well well-sm alert-danger").html("Please fill in both values.")}else{$(".btn").addClass("disabled");$(".clipboard-js-init").addClass("hidden");$("#psk").attr("class","progress progress-striped").html("<div class=\"progress-bar progress-bar-light active\"></div>").parent().removeClass("col-md-11").addClass("col-md-12")}var a=new PBKDF2(b,c,4096,32);a.deriveKey(status,print_psk)}', View::POS_HEAD);
$this->registerJs('function reset_psk(){$("#psk").attr("class","well well-sm").html("Not calculated yet.");$(".clipboard-js-init").addClass("hidden");$("#psk").parent().removeClass("col-md-11").addClass("col-md-12")};', View::POS_HEAD);
$this->registerJs('reset_psk();', View::POS_READY);
$this->registerJs('$("form input").keydown(function(e){if(e.keyCode==13){cal_psk();return false}});', View::POS_READY);
$this->registerJs('function PBKDF2(c,f,h,o){var m=rstr2binb(c);var d=f;var a=h;var j=10;var w=0;var v=o;var e=null;var r=20;var b=Math.ceil(v/r);var q=1;var n=new Array(16);var k=new Array(16);var p=new Array(0,0,0,0,0);var l="";var u=this;var t;var g;if(m.length>16){m=binb_sha1(m,c.length*chrsz)}for(var s=0;s<16;++s){n[s]=m[s]^909522486;k[s]=m[s]^1549556828}this.deriveKey=function(i,x){g=i;t=x;setTimeout(function(){u.do_PBKDF2_iterations()},0)};this.do_PBKDF2_iterations=function(){var A=j;if(a-w<j){A=a-w}for(var z=0;z<A;++z){if(w==0){var B=d+String.fromCharCode(q>>24&15)+String.fromCharCode(q>>16&15)+String.fromCharCode(q>>8&15)+String.fromCharCode(q&15);e=binb_sha1(n.concat(rstr2binb(B)),512+B.length*8);e=binb_sha1(k.concat(e),512+160)}else{e=binb_sha1(n.concat(e),512+e.length*32);e=binb_sha1(k.concat(e),512+160)}for(var x=0;x<e.length;++x){p[x]^=e[x]}w++}g((q-1+w/a)/b*100);if(w<a){setTimeout(function(){u.do_PBKDF2_iterations()},0)}else{if(q<b){l+=rstr2hex(binb2rstr(p));q++;p=new Array(0,0,0,0,0);w=0;setTimeout(function(){u.do_PBKDF2_iterations()},0)}else{var y=rstr2hex(binb2rstr(p));l+=y.substr(0,(v-(b-1)*r)*2);t(l)}}}};', View::POS_END);
$this->registerJs('var hexcase=0;var b64pad="";function hex_sha1(a){return rstr2hex(rstr_sha1(str2rstr_utf8(a)))}function hex_hmac_sha1(a,b){return rstr2hex(rstr_hmac_sha1(str2rstr_utf8(a),str2rstr_utf8(b)))}function sha1_vm_test(){return hex_sha1("abc").toLowerCase()=="a9993e364706816aba3e25717850c26c9cd0d89d"}function rstr_sha1(a){return binb2rstr(binb_sha1(rstr2binb(a),a.length*8))}function rstr_hmac_sha1(c,f){var e=rstr2binb(c);if(e.length>16){e=binb_sha1(e,c.length*8)}var a=Array(16),d=Array(16);for(var b=0;b<16;b++){a[b]=e[b]^909522486;d[b]=e[b]^1549556828}var g=binb_sha1(a.concat(rstr2binb(f)),512+f.length*8);return binb2rstr(binb_sha1(d.concat(g),512+160))}function rstr2hex(c){try{hexcase}catch(g){hexcase=0}var f=hexcase?"0123456789ABCDEF":"0123456789abcdef";var b="";var a;for(var d=0;d<c.length;d++){a=c.charCodeAt(d);b+=f.charAt((a>>>4)&15)+f.charAt(a&15)}return b}function str2rstr_utf8(c){var b="";var d=-1;var a,e;while(++d<c.length){a=c.charCodeAt(d);e=d+1<c.length?c.charCodeAt(d+1):0;if(55296<=a&&a<=56319&&56320<=e&&e<=57343){a=65536+((a&1023)<<10)+(e&1023);d++}if(a<=127){b+=String.fromCharCode(a)}else{if(a<=2047){b+=String.fromCharCode(192|((a>>>6)&31),128|(a&63))}else{if(a<=65535){b+=String.fromCharCode(224|((a>>>12)&15),128|((a>>>6)&63),128|(a&63))}else{if(a<=2097151){b+=String.fromCharCode(240|((a>>>18)&7),128|((a>>>12)&63),128|((a>>>6)&63),128|(a&63))}}}}}return b}function rstr2binb(b){var a=Array(b.length>>2);for(var c=0;c<a.length;c++){a[c]=0}for(var c=0;c<b.length*8;c+=8){a[c>>5]|=(b.charCodeAt(c/8)&255)<<(24-c%32)}return a}function binb2rstr(b){var a="";for(var c=0;c<b.length*32;c+=8){a+=String.fromCharCode((b[c>>5]>>>(24-c%32))&255)}return a}function binb_sha1(v,o){v[o>>5]|=128<<(24-o%32);v[((o+64>>9)<<4)+15]=o;var y=Array(80);var u=1732584193;var s=-271733879;var r=-1732584194;var q=271733878;var p=-1009589776;for(var l=0;l<v.length;l+=16){var n=u;var m=s;var k=r;var h=q;var f=p;for(var g=0;g<80;g++){if(g<16){y[g]=v[l+g]}else{y[g]=bit_rol(y[g-3]^y[g-8]^y[g-14]^y[g-16],1)}var z=safe_add(safe_add(bit_rol(u,5),sha1_ft(g,s,r,q)),safe_add(safe_add(p,y[g]),sha1_kt(g)));p=q;q=r;r=bit_rol(s,30);s=u;u=z}u=safe_add(u,n);s=safe_add(s,m);r=safe_add(r,k);q=safe_add(q,h);p=safe_add(p,f)}return Array(u,s,r,q,p)}function sha1_ft(e,a,g,f){if(e<20){return(a&g)|((~a)&f)}if(e<40){return a^g^f}if(e<60){return(a&g)|(a&f)|(g&f)}return a^g^f}function sha1_kt(a){return(a<20)?1518500249:(a<40)?1859775393:(a<60)?-1894007588:-899497514}function safe_add(a,d){var c=(a&65535)+(d&65535);var b=(a>>16)+(d>>16)+(c>>16);return(b<<16)|(c&65535)}function bit_rol(a,b){return(a<<b)|(a>>>(32-b))};', View::POS_END);

$model = new DynamicModel(['ssid', 'pass']);
$model->addRule('ssid', 'required', ['message' => 'SSID cannot be blank.']);
$model->addRule('pass', 'required', ['message' => 'WPA Passphrase cannot be blank.']);
$model->addRule('ssid', 'string', ['max'=>32]);
$model->addRule('pass', 'string', ['min'=>8, 'max'=>63]);
?>
<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<?= Html::tag('h1', Html::encode($this->title)) ?>

		<p>This WPA Pre-shared Key Calculator provides an easy way to convert a SSID and WPA Passphrase to the 256-bit pre-shared ("raw") key used for key derivation.<br />Type or paste in your SSID and WPA Passphrase below. Click 'Calculate' and wait a while as Javascript isn't known for its blistering cryptographic speed. The Pre-Shared Key will be calculated by your browser. <strong>None</strong> of this information will be sent over the network.</p>

		<?php $form = ActiveForm::begin([
				'id' => 'wpapsk',
				'action' => null,
				'options' => ['csrf' => false],
				'fieldConfig' => [
						'template' => "{label}{input}{error}",
						'labelOptions' => ['class' => 'control-label'],
				],
		]); ?>

		<?= $form->field($model, 'ssid', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('signal').'</span>{input}</div>{error}',
			])
			->label('SSID')
			->textInput(['autofocus' => true, 'id' => 'ssid', 'tabindex' => 1]) ?>

		<?= $form->field($model, 'pass', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>{error}',
			])
			->label('WPA Passphrase')
			->textInput(['id' => 'pass', 'tabindex' => 2]) ?>

		<div class="form-group field-psk">
			<?= Html::tag('label', 'Pre-Shared Key', ['class' => 'control-label']) ?>
			<div class="row">
				<div class="col-md-12">
					<?= Html::tag('div', 'Javascript is disabled in your web browser. This tool does not work without Javascript.', ['id' => 'psk']) ?>
				</div>
				<div class="col-md-1 text-right">
					<button class="btn btn-sm btn-primary clipboard-js-init hidden" data-clipboard-target="#psk" data-toggle="tooltip" data-placement="top" title="Copy to Clipboard" type="button"><?= Html::icon('copy') ?></button>
				</div>
			</div>
		</div>

		<div class="form-group text-right">
			<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 4, 'onclick' => 'reset_psk()']) ?>
			<?= Html::button('Calculate', ['class' => 'btn btn-primary', 'tabindex' => 3, 'onclick' => 'cal_psk()']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>
</div>
