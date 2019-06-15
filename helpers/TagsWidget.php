<?php

/**
 * Widget para Tags
 * @author Isan
 */
class TagsWidget {

    public static function widget($model, $attribute, $tags) {
        self::registerAssets();
        self::registerConfig($model, $attribute, $tags);

        echo Html::activeTextInput($model, $attribute);
    }

    private static function registerAssets() {
        Assets::registerCSS(['jquery-ui.min.css', 'jquery.tagsinput-revisited.css']);
        Assets::registerJS(['jquery-ui.min.js', 'jquery.tagsinput-revisited.js'], ['jquery']);
    }

    public static function registerConfig($model, $attribute, array $tags) {
        $tagsToString = json_encode($tags);
        $inputId = Html::getInputId($model, $attribute);

        $script = <<<JS
    jQuery('#{$inputId}').tagsInput({
     'delimiter': [',', ';'],
     placeholder: '{$model->getAttributeLabel($attribute)}',
     'autocomplete': {
        source: {$tagsToString}
     },
     'unique': true,
     'minChars': 2,
     'maxChars': 10,
     'limit': 5,
     'validationPattern': new RegExp('^[0-9 a-zA-Z \s]+$')
    });
JS;
        Assets::registerInlineJS($script);
    }

}
