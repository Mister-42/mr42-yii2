<?php

use yii\helpers\Html;
use yii\helpers\Url;
use mister42\assets\HighlightAsset;

HighlightAsset::register($this);
$this->linkTags[] = Html::tag('base', '', ['href' => Url::to(['extensions/index', 'name' => $name]).'/']);

$this->registerJs("
    $(\"[data-toggle='offcanvas']\").click(function () {
      $('.row-offcanvas').toggleClass('active')
    });

    $('.has-children.active + div').addClass('active-parent');
");

if (!empty($title)) {
    $this->title = $title;
}

?>
<div class="container api-content">
    <div class="row visible-xs">
        <div class="col-md-12">
            <p class="pull-right topmost">
                <button type="button" title="Toggle Side-Nav" class="btn btn-primary btn-xs" data-toggle="offcanvas">SideNav</button>
            </p>
        </div>
    </div>

    <?= $content ?>
</div>

<?php

$this->registerJs(
    <<<'JS'

$(".api-content a.toggle").on('click', function () {
    var $this = $(this);
    if ($this.hasClass('properties-hidden')) {
        $this.text($this.text().replace(/Show/,'Hide'));
        $this.parents(".summary").find(".inherited").show();
        $this.removeClass('properties-hidden');
    } else {
        $this.text($this.text().replace(/Hide/,'Show'));
        $this.parents(".summary").find(".inherited").hide();
        $this.addClass('properties-hidden');
    }

    return false;
});


JS
);
