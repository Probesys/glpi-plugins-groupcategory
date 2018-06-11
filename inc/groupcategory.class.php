<?php

class PluginGroupcategoryGroupcategory extends CommonDBTM
{

    /**
     * All categories
     * @var array
     */
    static private $_all_categories = [];

    /**
     * Get all categories
     *
     * @return array
     */
    public static function getAllCategories()
    {
        if (empty(PluginGroupcategoryGroupcategory::$_all_categories)) {
            $category = new ITILCategory();
            $categories = $category->find("TRUE", "completename ASC, level ASC, id ASC");

            PluginGroupcategoryGroupcategory::$_all_categories = $categories;
        }

        return PluginGroupcategoryGroupcategory::$_all_categories;
    }

    /**
     * Get the selected categories for a group
     *
     * @param  Group $group
     * @return array
     */
    public static function getSelectedCategoriesForGroup(Group $group)
    {
        $group_category = new PluginGroupcategoryGroupcategory();

        if ($group_category->getFromDBByQuery("WHERE TRUE AND group_id = " . $group->getId())) {
            $category_ids = explode(', ', $group_category->fields['category_ids']);

            $all_categories = PluginGroupcategoryGroupcategory::getAllCategories();
            $selected_categories = [];

            foreach ($all_categories as $details) {
                if (in_array($details['id'], $category_ids)) {
                    $selected_categories[$details['id']] = $details['completename'];
                }
            }
        } else {
            $selected_categories = [];
        }

        return $selected_categories;
    }

    /**
     * Get the categories for a user
     *
     * @param int $user_id
     * @return array
     */
    public static function getUserCategories($user_id)
    {
        $user_categories = [];

        $user = new User();

        if ($user->getFromDB($user_id)) {
            $user_groups = Group_User::getUserGroups($user_id);

            foreach ($user_groups as $group_data) {
                $group = new Group();

                if ($group->getFromDB($group_data['id'])) {
                    $categories = PluginGroupcategoryGroupcategory::getSelectedCategoriesForGroup($group);
                    $user_categories += $categories;
                }
            }
        }

        return $user_categories;
    }
}
