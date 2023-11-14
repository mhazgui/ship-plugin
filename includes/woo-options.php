<?php
// Meta-Box Generator
// How to use: $meta_value = get_post_meta( $post_id, $field_id, true );
// Example: get_post_meta( get_the_ID(), "my_metabox_field", true );
class NavexAdvancedOptionsMetabox
{
    private $screens = ["shop_order"];

    private $fields = [
        [
            "label" => "Nom",
            "id" => "nom",
            "type" => "text",
            "default" => "",
        ],
        [
            "label" => "Gouvernerat",
            "id" => "gouvernerat",
            "type" => "select",
            "options" => [
                "Ariana",
                "Béja",
                "Ben Arous",
                "Bizerte",
                "Gabès",
                "Gafsa",
                "Jendouba",
                "Kairouan",
                "Kasserine",
                "Kébili",
                "La Manouba",
                "Le Kef",
                "Mahdia",
                "Médenine",
                "Monastir",
                "Nabeul",
                "Sfax",
                "Sidi Bouzid",
                "Siliana",
                "Sousse",
                "Tataouine",
                "Tozeur",
                "Tunis",
                "Zaghouan",
            ],
            "default" => [],
        ],
        [
            "label" => "Ville",
            "id" => "ville",
            "type" => "text",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "Addresse",
            "id" => "adresse",
            "type" => "text",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "Tel",
            "id" => "tel",
            "type" => "text",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "Tel 2",
            "id" => "tel2",
            "type" => "text",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "Désignation",
            "id" => "designation",
            "type" => "textarea",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "Article",
            "id" => "article",
            "type" => "textarea",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "Nombre d'articles",
            "id" => "nb_article",
            "type" => "text",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "Prix",
            "id" => "prix",
            "type" => "text",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "Message",
            "id" => "msg",
            "type" => "text",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "Échange",
            "id" => "echange",
            "type" => "text",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "Nbr Échange",
            "id" => "nb_echange",
            "type" => "text",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "",
            "value" => "Save & Submit",
            "id" => "submit_form_data",
            "type" => "button",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "Export",
            "value" => "",
            "id" => "navex_export_btn",
            "type" => "href",
            "default" => "",
            "attributes" => ""
        ],
        [
            "label" => "Delete",
            "value" => "",
            "id" => "navex_delete_btn",
            "type" => "href",
            "default" => "",
            "attributes" => ""
        ]
    ];

    public function __construct()
    {
        add_action("add_meta_boxes", [$this, "add_meta_boxes"]);
        add_action("save_post", [$this, "save_fields"]);
    }

    public function add_meta_boxes()
    {
        foreach ($this->screens as $s) {
            add_meta_box(
                "NavexAdvancedOptions",
                __("Navex Advanced Options", "navex"),
                [$this, "meta_box_callback"],
                $s,
                "normal",
                "default"
            );
        }
    }

    public function meta_box_callback($post)
    {
        wp_nonce_field("AdvancedOptions_data", "AdvancedOptions_nonce");
        $this->field_generator($post);
    }

    public function field_generator($post)
    {
        $output = "";
        $postOrderNonce = wp_create_nonce("navex_post_order_nonce");
        $deleteOrderNonce = wp_create_nonce("navex_delete_order_nonce");
        $order = new WC_Order( $post->ID );
        $productNames = "";
        foreach ( $order->get_items() as $item_id => $item ) {
            $productNames .= $item->get_quantity() . ' - ' . $item->get_name() . '&#13;';
        }

        if ($order) {
            $exportUrl = get_post_meta($post->ID, 'order_export_url', true);
            foreach ($this->fields as $field) {
                $label =
                    '<label for="' .
                    $field["id"] .
                    '">' .
                    $field["label"] .
                    "</label></br>";
                $meta_value = ""; //get_post_meta($post->ID, $field["id"], true);
                if (empty($meta_value)) {
                    if ($field["id"] === "prix") {
                        $field["default"] = $order->total;
                    }
                    if ($field["id"] === "nom") {
                        $field["default"] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                    }
                    if ($field["id"] === "adresse") {
                        $field["default"] = $order->get_billing_address_1();
                    }
                    if ($field["id"] === "tel") {
                        $field["default"] = $order->get_billing_phone();
                    }
                    if ($field["id"] === "ville") {
                        $field["default"] = $order->get_billing_city();
                    }
                    if ($field["id"] === "article" || $field["id"] === "designation") {
                        $field["default"] = $productNames;
                    }
                    if ($field["id"] === "nb_article") {
                        $field["default"] = $order->get_item_count();
                    }
                    if ($field["id"] === "href") {
                        $field["value"] = $exportUrl;
                        $field["default"] = $exportUrl;
                    }

                    if ($field["id"] === "submit_form_data") {
                        $field["attributes"] = 'data-order_id="' .$post->ID. '" data-nonce="'. $postOrderNonce .'"';
                    }
                    if ($field["id"] === "navex_delete_btn") {
                        $field["attributes"] = 'data-order_id="' .$post->ID. '" data-nonce="'. $deleteOrderNonce .'"';
                        if ($exportUrl) {
                            $url_components = parse_url($exportUrl);
                            parse_str($url_components['code'], $params);
                            $field["attributes"] .= ' data-order_code="'. $params['code'] .'"';
                        }
                    }
                    if (isset($field["default"])) {
                        $meta_value = $field["default"];
                    }
                }

                switch ($field["type"]) {
                    case "select":
                        $input = sprintf(
                            '<select id="%s" name="%s">',
                            $field["id"],
                            $field["id"],
                        );
                        foreach ($field["options"] as $key => $value) {
                            $field_value = !is_numeric($key) ? $key : $value;
                            $input .= sprintf(
                                '<option %s value="%s">%s</option>',
                                $meta_value === $field_value ? "selected" : "",
                                $field_value,
                                $value
                            );
                        }
                        $input .= "</select></br>";
                        break;

                    case "button":
                        $label = '';
                        $input = sprintf(
                            '<button %s id="%s" type="%s" class="button button-primary calculate-action" style="margin-top: 18px; margin-right: 10px;">%s</button>',
                            $field["attributes"],
                            $field["id"],
                            $field["type"],
                            $field["value"]
                        );
                        break;
                    case "href":
                        $label = '';
                        $input = sprintf(
                            '<a %s id="%s" href="%s" target="_blank" class="button button-primary export-action" style="margin-top: 18px; margin-right: 10px;">%s</a>',
                            $field["attributes"],
                            $field["id"],
                            $exportUrl,
                            $field["label"]
                        );
                        break;

                    case "textarea":
                        $input = sprintf(
                            '<textarea %s id="%s" name="%s">%s</textarea></br>',
                            $field["type"] !== "color"
                                ? 'style="width: 100%"'
                                : "",
                            $field["id"],
                            $field["id"],
                            $meta_value
                        );
                        break;

                    case "hidden":
                        $input = sprintf(
                            '<input type="hidden" id="%s" name="%s" value="%s">',
                            $field["id"],
                            $field["id"],
                            $meta_value
                        );
                        break;

                    default:
                        $input = sprintf(
                            '<input %s id="%s" name="%s" type="%s" value="%s"></br>',
                            $field["type"] !== "color"
                                ? 'style="width: 100%"'
                                : "",
                            $field["id"],
                            $field["id"],
                            $field["type"],
                            $meta_value
                        );
                }
                $output .= $this->format_rows($label, $input, $field["type"]);
            }
            ob_start();
            echo '<table class="form-table"><tbody>' .
                    $output .
                "</tbody></table>";
        }
    }

    public function format_rows($label, $input, $fieldType)
    {
        if ($fieldType !== 'hidden' || $fieldType !== 'button' || $fieldType !== 'href') {
            return $label . $input;
        }
        return $input;
    }

    public function save_fields($post_id)
    {
        if (!isset($_POST["AdvancedOptions_nonce"])) {
            return $post_id;
        }
        $nonce = $_POST["AdvancedOptions_nonce"];
        if (!wp_verify_nonce($nonce, "AdvancedOptions_data")) {
            return $post_id;
        }
        if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
            return $post_id;
        }
        foreach ($this->fields as $field) {
            if (isset($_POST[$field["id"]])) {
                switch ($field["type"]) {
                    case "email":
                        $_POST[$field["id"]] = sanitize_email(
                            $_POST[$field["id"]]
                        );
                        break;
                    case "text":
                        $_POST[$field["id"]] = sanitize_text_field(
                            $_POST[$field["id"]]
                        );
                        break;
                }
                //update_post_meta($post_id, $field["id"], $_POST[$field["id"]]);
            } elseif ($field["type"] === "checkbox") {
                /*update_post_meta(
                    $post_id,
                    $field["navex_order_meta"]["id"],
                    "0"
                );*/
            }
        }
    }

    /**
     * Returns an option value.
     */
    protected function get_option_value($option_name)
    {
        $option = get_option($this->option_name);
        if (!array_key_exists($option_name, $option)) {
            return array_key_exists("default", $this->settings[$option_name])
                ? $this->settings[$option_name]["default"]
                : "";
        }
        return $option[$option_name];
    }
}

if (class_exists("NavexAdvancedOptionsMetabox")) {
    new NavexAdvancedOptionsMetabox;
}