<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumStatistics {
    private static $asgarosforum = null;

    public function __construct($object) {
		self::$asgarosforum = $object;

        add_action('init', array($this, 'initialize'));
    }

    public function initialize() {
        // Empty ...
    }

    public static function showStatistics() {
        // Check if this functionality is enabled.
        if (self::$asgarosforum->options['show_statistics']) {
            $data = self::getData();
            echo '<div id="statistics">';
                echo '<div class="title-element title-element-dark dashicons-before dashicons-chart-bar">'.__('Statistics', 'asgaros-forum').'</div>';
                echo '<div id="statistics-body">';
                    echo '<div id="statistics-elements">';
                        self::renderStatisticsElement(__('Topics', 'asgaros-forum'), $data->topics, 'dashicons-editor-alignleft');
                        self::renderStatisticsElement(__('Posts', 'asgaros-forum'), $data->posts, 'dashicons-format-quote');

                        if (self::$asgarosforum->options['count_topic_views']) {
                            self::renderStatisticsElement(__('Views', 'asgaros-forum'), $data->views, 'dashicons-visibility');
                        }

                        self::renderStatisticsElement(__('Users', 'asgaros-forum'), $data->users, 'dashicons-groups');
                        self::$asgarosforum->online->render_statistics_element();
                        do_action('asgarosforum_statistics_custom_element');
                    echo '</div>';
                    self::$asgarosforum->online->render_online_information();
                echo '</div>';
                do_action('asgarosforum_statistics_custom_content_bottom');
                echo '<div class="clear"></div>';
            echo '</div>';
        }
    }

    public static function getData() {
        $queryTopics = 'SELECT COUNT(*) FROM '.self::$asgarosforum->tables->topics;
        $queryPosts = 'SELECT COUNT(*) FROM '.self::$asgarosforum->tables->posts;

        $queryViews = '0';

        if (self::$asgarosforum->options['count_topic_views']) {
            $queryViews = 'SELECT SUM(views) FROM '.self::$asgarosforum->tables->topics;
        }

        $data = self::$asgarosforum->db->get_row("SELECT ({$queryTopics}) AS topics, ({$queryPosts}) AS posts, ({$queryViews}) AS views");
        $data->users = self::$asgarosforum->countUsers();
        return $data;
    }

    public static function renderStatisticsElement($title, $data, $iconClass) {
        echo '<div class="statistics-element">';
            echo '<div class="element-number dashicons-before '.$iconClass.'">'.number_format_i18n($data).'</div>';
            echo '<div class="element-name">'.$title.'</div>';
        echo '</div>';
    }
}
