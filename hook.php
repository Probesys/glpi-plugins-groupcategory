<?php

/**
 * Install the plugin
 *
 * @return boolean
 */
function plugin_groupcategory_install()
{
    global $DB;

    if (!TableExists(getTableForItemType('PluginGroupcategoryGroupcategory'))) {
        $create_table_query = "
            CREATE TABLE IF NOT EXISTS `" . getTableForItemType('PluginGroupcategoryGroupcategory') . "`
            (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `group_id` INT(11) NOT NULL,
                `category_ids` TEXT NOT NULL,
                PRIMARY KEY (`id`),
                INDEX (`group_id`)
            )
            COLLATE='utf8_unicode_ci'
            ENGINE=MyISAM
        ";
        $DB->query($create_table_query) or die($DB->error());
    }

    return true;
}

/**
 * Uninstall the plugin
 *
 * @return boolean
 */
function plugin_groupcategory_uninstall()
{
    global $DB;

    $tables_to_drop = [
        getTableForItemType('PluginGroupcategoryGroupcategory'),
    ];

    $drop_table_query = "DROP TABLE IF EXISTS `" . implode('`, `', $tables_to_drop) . "`";

    return $DB->query($drop_table_query) or die($DB->error());
}

/**
 * Hook callback when an item is shown
 *
 * @param array $post_show_data
 */
function plugin_groupcategory_post_show_item(array $post_show_data)
{
    if (
        !empty($post_show_data['item'])
        && is_object($post_show_data['item'])
    ) {
        switch (get_class($post_show_data['item'])) {
            case 'Group':
                plugin_groupcategory_post_show_group($post_show_data['item']);
                break;

            case 'Ticket':
                plugin_groupcategory_post_show_ticket($post_show_data['item']);
                break;

            default:
                // nothing to do
        }
    }
}

/**
 * Hook callback when a group is shown
 *
 * @param Group $group
 */
function plugin_groupcategory_post_show_group(Group $group)
{
    if ($group->getId() > 0) {
        // it's an existing group!

        $categories = PluginGroupcategoryGroupcategory::getAllCategories();
        $selected_categories = PluginGroupcategoryGroupcategory::getSelectedCategoriesForGroup($group);

        echo '<table>' . "\n";
        echo '<tbody id="groupcategory_content">' . "\n";

        if (true) {
            echo '<tr class="tab_bg_1">' . "\n";

            echo '<th colspan="2" class="subheader">';
            echo 'Catégories refusées';
            echo '</th>' . "\n";

            echo '<th colspan="2" class="subheader">';
            echo 'Catégories autorisées';
            echo '</th>' . "\n";

            echo '</tr>' . "\n";
        }

        if (true) {
            echo '<tr class="tab_bg_1">' . "\n";

            if (true) {
                echo '<td colspan="2">' . "\n";

                echo '<input type="hidden" name="groupcategory_allowed_categories" id="groupcategory_allowed_categories_ids" value="' . implode(', ', array_keys($selected_categories)) . '" />';

                echo '<div>';
                echo '<input type="button" class="submit" id="groupcategory_allow_categories" value="Autoriser >" style="padding: 10px" />';
                echo '</div>' . "\n";

                echo '<select id="groupcategory_denied_categories" style="min-width: 150px; height: 150px; margin-top: 15px;" multiple>' . "\n";

                foreach ($categories as $details) {
                    if (!isset($selected_categories[$details['id']])) {
                        echo '<option value="' . $details['id'] . '">';
                        echo $details['completename'];
                        echo '</option>' . "\n";
                    }
                }

                echo '</select>' . "\n";

                echo '</td>' . "\n";
            }

            if (true) {
                echo '<td colspan="2">' . "\n";

                echo '<div>';
                echo '<input type="button" class="submit" id="groupcategory_deny_categories" value="< Refuser" style="padding: 10px" />';
                echo '</div>' . "\n";

                echo '<select id="groupcategory_allowed_categories" style="min-width: 150px; height: 150px; margin-top: 15px;" multiple>' . "\n";

                foreach ($selected_categories as $category_id => $completename) {
                    echo '<option value="' . $category_id . '">';
                    echo $completename;
                    echo '</option>' . "\n";
                }

                echo '</select>' . "\n";

                echo '</td>' . "\n";
            }
        }

        echo '</tbody>' . "\n";
        echo '</table>' . "\n";

        $js_block = '
            var _groupcategory_content = $("#groupcategory_content");
            $(_groupcategory_content.html()).detach().insertBefore("#mainformtable .footerRow");
            _groupcategory_content.remove();

            var _groupcategory_selected_categories = {
                "denied": [],
                "allowed": []
            };

            var _groupcategory_denied_categories = $("#groupcategory_denied_categories");
            var _groupcategory_allowed_categories = $("#groupcategory_allowed_categories");

            var _groupcategory_allowed_categories_ids_elm = $("#groupcategory_allowed_categories_ids");
            var _groupcategory_allowed_categories_ids = [];

            if (_groupcategory_allowed_categories_ids_elm.val()) {
                _groupcategory_allowed_categories_ids = _groupcategory_allowed_categories_ids_elm.val().split(", ");
            }

            _groupcategory_denied_categories.on("change", function(e) {
                var selection = $(this).val();

                if (selection === null) {
                    selection = [];
                }

                _groupcategory_selected_categories.denied = selection;
            });

            _groupcategory_allowed_categories.on("change", function(e) {
                var selection = $(this).val();

                if (selection === null) {
                    selection = [];
                }

                _groupcategory_selected_categories.allowed = selection;
            });

            $("#groupcategory_allow_categories").on("click", function(e) {
                if (_groupcategory_selected_categories.denied.length) {
                    var
                        current_category_id,
                        current_category_option
                    ;

                    for (var i in _groupcategory_selected_categories.denied) {
                        current_category_id = _groupcategory_selected_categories.denied[i];
                        current_category_option = $("option[value=" + current_category_id + "]", _groupcategory_denied_categories);
                        _groupcategory_allowed_categories.append("<option value=\"" + current_category_id + "\">" + current_category_option.text() + "</option>");
                        current_category_option.remove();

                        _groupcategory_allowed_categories_ids.push(current_category_id);
                    }

                    _groupcategory_allowed_categories_ids_elm.val(_groupcategory_allowed_categories_ids.join(", "));
                }
            });

            $("#groupcategory_deny_categories").on("click", function(e) {
                if (_groupcategory_selected_categories.allowed.length) {
                    var
                        current_category_id,
                        current_category_option,
                        allowed_category_idx
                    ;

                    for (var i in _groupcategory_selected_categories.allowed) {
                        current_category_id = _groupcategory_selected_categories.allowed[i];
                        current_category_option = $("option[value=" + current_category_id + "]", _groupcategory_allowed_categories);
                        _groupcategory_denied_categories.append("<option value=\"" + current_category_id + "\">" + current_category_option.text() + "</option>");
                        current_category_option.remove();

                        allowed_category_idx = _groupcategory_allowed_categories_ids.indexOf(current_category_id);

                        if (allowed_category_idx > -1) {
                            _groupcategory_allowed_categories_ids.splice(allowed_category_idx, 1);
                        }
                    }

                    _groupcategory_allowed_categories_ids_elm.val(_groupcategory_allowed_categories_ids.join(", "));
                }
            });
        ';

        echo Html::scriptBlock($js_block);
    }
}

/**
 * Hook callback before a group is updated
 *
 * @param Group $group
 */
function plugin_groupcategory_group_update(Group $group)
{
    if (isset($group->input['groupcategory_allowed_categories'])) {
        $allowed_categories_ids = trim($group->input['groupcategory_allowed_categories']);

        $selected_categories = PluginGroupcategoryGroupcategory::getSelectedCategoriesForGroup($group);
        $selected_categories_ids = implode(', ', array_keys($selected_categories));

        if ($allowed_categories_ids != $selected_categories_ids) {
            $group_category = new PluginGroupcategoryGroupcategory();
            $exists = $group_category->getFromDBByQuery("WHERE TRUE AND group_id = " . $group->getId());
            $group_update_params = [
                'group_id' => $group->getId(),
                'category_ids' => $allowed_categories_ids,
            ];

            if ($exists) {
                $group_update_params['id'] = $group_category->getId();
                $group_category->update($group_update_params, [], false);
            } else {
                $group_category->add($group_update_params, [], false);
            }
        }
    }
}

/**
 * Hook callback when a ticket is shown
 *
 * @param Ticket $ticket
 */
function plugin_groupcategory_post_show_ticket(Ticket $ticket)
{
    $user_categories = PluginGroupcategoryGroupcategory::getUserCategories();
    $js_block = '
        var category_container = $("#show_category_by_type");

        if ($(".select2-choice", category_container).length) {
            var
                allowed_categories = ' . json_encode($user_categories) . ',
                select2_elm = $("[id^=dropdown_itilcategories_id]", category_container),
                previous_formatResult = select2_elm.data("select2").opts.formatResult
            ;

            select2_elm.data("select2").opts.formatResult = function(result, container, query, escapeMarkup) {
                var format_result = true;

                if (
                    result.id !== undefined
                    && result.id !== 0
                    && allowed_categories[result.id] === undefined
                ) {
                    format_result = false;
                }

                if (format_result) {
                    return previous_formatResult(result, container, query, escapeMarkup);
                }
            };
        }
    ';

    echo Html::scriptBlock($js_block);
}
