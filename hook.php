<?php

/**
 * Install the plugin
 *
 * @return boolean
 */
function plugin_groupcategory_install() {
    global $DB;

    if (!$DB->tableExists(getTableForItemType('PluginGroupcategoryGroupcategory'))) {
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
function plugin_groupcategory_uninstall() {
    global $DB;

    $tables_to_drop = [
      getTableForItemType('PluginGroupcategoryGroupcategory'),
    ];

    $drop_table_query = "DROP TABLE IF EXISTS `" . implode('`, `', $tables_to_drop) . "`";

    return $DB->query($drop_table_query) or die($DB->error());
}

/**
 * Hook callback when a group is shown
 *
 * @param Group $group
 */
function plugin_groupcategory_post_show_group(Group $group) {

    if ($group->getId() > 0) {

        $categories = PluginGroupcategoryGroupcategory::getAllCategories();
        $selected_categories = PluginGroupcategoryGroupcategory::getSelectedCategoriesForGroup($group);
        $dom = '';
        $dom .= '<div id="groupcategory_content">';
        $dom .= '<table class="tab_cadre_fixe" >' . "\n";
        $dom .= '<tbody>' . "\n";

        if (true) {
            $dom .= '<tr class="tab_bg_1">' . "\n";

            $dom .= '<th colspan="2" class="subheader">';
            $dom .= 'Catégories refusées';
            $dom .= '</th>' . "\n";

            $dom .= '<th colspan="2" class="subheader">';
            $dom .= 'Catégories autorisées';
            $dom .= '</th>' . "\n";

            $dom .= '</tr>' . "\n";
        }

        if (true) {
            $dom .= '<tr class="tab_bg_1">' . "\n";

            if (true) {
                $dom .= '<td colspan="2">' . "\n";

                $dom .= '<input type="hidden" name="groupcategory_allowed_categories" id="groupcategory_allowed_categories_ids" value="' . implode(', ', array_keys($selected_categories)) . '" />';

                $dom .= '<div>';
                $dom .= '<input type="button" class="submit" id="groupcategory_allow_categories" value="Autoriser >" style="padding: 10px" />';
                $dom .= '</div>' . "\n";

                $dom .= '<select id="groupcategory_denied_categories" style="min-width: 150px; height: 150px; margin-top: 15px;" multiple>' . "\n";

                foreach ($categories as $details) {
                    if (!isset($selected_categories[$details['id']])) {
                        $dom .= '<option value="' . $details['id'] . '">';
                        $dom .= $details['completename'];
                        $dom .= '</option>' . "\n";
                    }
                }

                $dom .= '</select>' . "\n";

                $dom .= '</td>' . "\n";
            }

            if (true) {
                $dom .= '<td colspan="2">' . "\n";

                $dom .= '<div>';
                $dom .= '<input type="button" class="submit" id="groupcategory_deny_categories" value="< Refuser" style="padding: 10px" />';
                $dom .= '</div>' . "\n";

                $dom .= '<select id="groupcategory_allowed_categories" style="min-width: 150px; height: 150px; margin-top: 15px;" multiple>' . "\n";

                foreach ($selected_categories as $category_id => $completename) {
                    $dom .= '<option value="' . $category_id . '">';
                    $dom .= $completename;
                    $dom .= '</option>' . "\n";
                }

                $dom .= '</select>' . "\n";

                $dom .= '</td>' . "\n";
            }
        }

        $dom .= '</tbody>' . "\n";
        $dom .= '</table>' . "\n";
        $dom .= '</div>' . "\n";

        echo $dom;

        $js_block = '
            var _groupcategory_content = $("#groupcategory_content");
            $(_groupcategory_content.html()).detach().insertAfter("table#mainformtable");
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
function plugin_groupcategory_group_update(Group $group) {

    if (isset($group->input['groupcategory_allowed_categories'])) {
        $allowed_categories_ids = trim($group->input['groupcategory_allowed_categories']);

        $selected_categories = PluginGroupcategoryGroupcategory::getSelectedCategoriesForGroup($group);
        $selected_categories_ids = implode(', ', array_keys($selected_categories));

        if ($allowed_categories_ids != $selected_categories_ids) {
            $group_category = new PluginGroupcategoryGroupcategory();
            //$exists = $group_category->getFromDBByQuery("WHERE TRUE AND group_id = " . $group->getId());
            $exists = $group_category->getFromDBByCrit(["group_id" => $group->getId()]);
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
function plugin_groupcategory_post_show_ticket(Ticket $ticket) {
    global $CFG_GLPI;
    $get_user_categories_url = rtrim($CFG_GLPI['root_doc'], '/') . '/plugins/groupcategory/ajax/get_user_categories.php';

    $js_block = '
        //console.log("plugin_groupcategory_post_show_ticket");
        var requester_user_id = 0;
        ';
    $user_id = $_SESSION['glpiID'];
    $js_block .= 'var requester_user_id = ' . $user_id . ';';
    if ($_SESSION["glpiactiveprofile"]["interface"] == "central") {
            
        $js_block .= '
            var requester_user_id_input = $("select[id^=dropdown__users_id_requester]");
            if (requester_user_id_input.length) {
                var requester_user_id = parseInt(requester_user_id_input.val());
            }
            ';
    }
    //$js_block .= 'console.log(requester_user_id);';        
    $js_block .= '        
        if (requester_user_id) {            
            $.ajax("' . $get_user_categories_url . '", {
                method: "POST",
                cache: false,
                data: {
                    requester_user_id: requester_user_id
                },
                complete: function(responseObj, status) {
                    if ( status == "success"  && responseObj.responseText.length) 
                    {
                        try {
                            var allowed_categories = $.parseJSON(responseObj.responseText);
                            displayAllowedCategories(allowed_categories);
                        } catch (e) {
                        }
                    }
                }
            });
        }

        function displayAllowedCategories(allowed_categories) {
            
            var category_container = $("#show_category_by_type");
            idSelectItil = $("select[name=itilcategories_id]").attr(\'id\');
            $("#"+idSelectItil).empty().select2({
                data: allowed_categories,                
            });
            
        };
    ';

    echo Html::scriptBlock($js_block);
}
