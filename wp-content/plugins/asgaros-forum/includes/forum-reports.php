<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumReports {
    private $asgarosforum = null;
    private $current_user_reports = false;

    public function __construct($object) {
        $this->asgarosforum = $object;

        add_action('asgarosforum_breadcrumbs_reports', array($this, 'add_breadcrumbs'));
    }

    public function add_breadcrumbs() {
        $element_link = $this->asgarosforum->get_link('reports');
        $element_title = __('Reports', 'asgaros-forum');
        $this->asgarosforum->breadcrumbs->add_breadcrumb($element_link, $element_title);
    }

    public function render_report_button($post_id, $topic_id) {
        if ($this->asgarosforum->options['reports_enabled']) {
            // Only show a report button when the user is logged-in.
            if (is_user_logged_in()) {
                $reporter_id = get_current_user_id();

                if (!$this->report_exists($post_id, $reporter_id)) {
                    $report_message = __('Are you sure that you want to report this post?', 'asgaros-forum');
                    $report_href = $this->asgarosforum->rewrite->get_link('topic', $topic_id, array('post' => $post_id, 'report_add' => 1, 'part' => ($this->asgarosforum->current_page + 1)), '#postid-'.$post_id);

                    echo '<a href="'.$report_href.'" title="'.__('Report Post', 'asgaros-forum').'" onclick="return confirm(\''.$report_message.'\');">';
                        echo '<span class="report-link dashicons-before dashicons-warning">';
                        echo '<span class="screen-reader-text">'.__('Click to report post.', 'asgaros-forum').'</span>';
                        echo '</span>';
                    echo '</a>';
                } else {
                    echo '<span class="report-exists dashicons-before dashicons-warning" title="'.__('You reported this post.', 'asgaros-forum').'"></span>';
                }

                echo '&nbsp;&middot;&nbsp;';
            }
        }
    }

    public function add_report($post_id, $reporter_id) {
        // Only add a report when the post exists ...
        if ($this->asgarosforum->content->post_exists($post_id)) {
            // ... and the user is logged in ...
            if (is_user_logged_in()) {
                // ... and when there is not already a report from the user.
                if (!$this->report_exists($post_id, $reporter_id)) {
                    $this->asgarosforum->db->insert($this->asgarosforum->tables->reports, array('post_id' => $post_id, 'reporter_id' => $reporter_id), array('%d', '%d'));

                    // Send notification to site owner about new report.
                    $this->send_notification($post_id, $reporter_id);

                    // Add the value also to the reports array.
                    $this->current_user_reports[] = $post_id;
                }
            }
        }
    }

    public function send_notification($post_id, $reporter_id) {
        if ($this->asgarosforum->options['reports_notifications']) {
            $report = $this->get_report($post_id, $reporter_id);

            $author_name = $this->asgarosforum->getUsername($report['author_id']);
            $reporter = get_userdata($report['reporters']);

            $replacements = array(
                '###AUTHOR###'      => $author_name,
                '###REPORTER###'    => $reporter->display_name,
                '###LINK###'        => '<a href="'.$report['post_link'].'">'.$report['post_link'].'</a>',
                '###TITLE###'       => $report['topic_name'],
                '###CONTENT###'     => $report['post_text_raw']
            );

            $notification_subject = __('New report', 'asgaros-forum');
            $notification_message = __('Hello ###USERNAME###,<br><br>There is a new report.<br><br>Topic:<br>###TITLE###<br><br>Post:<br>###CONTENT###<br><br>Post Author:<br>###AUTHOR###<br><br>Reporter:<br>###REPORTER###<br><br>Link:<br>###LINK###', 'asgaros-forum');

            // Get receivers of admin-notifications.
            $receivers_admin_notifications = explode(',', $this->asgarosforum->options['receivers_admin_notifications']);

            // If found some, add them to the mailing-list.
            if (!empty($receivers_admin_notifications)) {
                foreach ($receivers_admin_notifications as $mail) {
                    $this->asgarosforum->notifications->add_to_mailing_list($mail);
                }
            }

            // Send notifications about new report.
            $this->asgarosforum->notifications->send_notifications($this->asgarosforum->notifications->mailing_list, $notification_subject, $notification_message, $replacements);
        }
    }

    public function remove_report($post_id) {
        // Ensure that the current user is at least a moderator.
        if (!$this->asgarosforum->permissions->isModerator('current')) {
            return;
        }

        $this->asgarosforum->db->delete($this->asgarosforum->tables->reports, array('post_id' => $post_id), array('%d'));
    }

    public function report_exists($post_id, $reporter_id) {
        // Load records of user first.
        $user_reports = $this->get_reports_of_user($reporter_id);

        if (in_array($post_id, $user_reports)) {
            return true;
        }

        return false;
    }

    public function get_reports_of_user($reporter_id) {
        if ($this->current_user_reports === false) {
            $this->current_user_reports = $this->asgarosforum->db->get_col($this->asgarosforum->db->prepare('SELECT post_id FROM '.$this->asgarosforum->tables->reports.' WHERE reporter_id = %d', $reporter_id));
        }

        return $this->current_user_reports;
    }

    // Returns all reported posts with an array of reporting users.
    public function get_reports() {
        $reports = array();

        // Get accessible categories first.
        $ids_categories = $this->asgarosforum->content->get_categories_ids();

        if (!empty($ids_categories)) {
            $ids_categories = implode(',', $ids_categories);

            $result = $this->asgarosforum->db->get_results("SELECT r.post_id, r.reporter_id FROM {$this->asgarosforum->tables->reports} AS r, {$this->asgarosforum->tables->posts} AS p, {$this->asgarosforum->tables->forums} AS f WHERE r.post_id = p.id AND p.forum_id = f.id AND f.parent_id IN ({$ids_categories}) ORDER BY r.post_id DESC;");

            foreach ($result as $report) {
                $reports[$report->post_id][] = $report->reporter_id;
            }
        }

        return $reports;
    }

    // Returns data of a specific report.
    public function get_report($post_id, $reporter_ids) {
        $post_object    = $this->asgarosforum->content->get_post($post_id);
        $topic_object   = $this->asgarosforum->content->get_topic($post_object->parent_id);
        $post_link      = $this->asgarosforum->rewrite->get_post_link($post_id, $topic_object->id, false, array('highlight_post' => $post_id));

        $report = array(
            'post_id'       => $post_id,
            'post_text'     => esc_html(stripslashes(strip_tags($post_object->text))),
            'post_text_raw' => wpautop(stripslashes($post_object->text)),
            'post_link'     => $post_link,
            'topic_name'    => esc_html(stripslashes($topic_object->name)),
            'author_id'     => $post_object->author_id,
            'reporters'     => $reporter_ids
        );

        return $report;
    }

    public function count_reports() {
        $result = $this->get_reports();

        return count($result);
    }

    // Shows an info-message when there are new reports.
    public function notice_for_moderators() {
        // Ensure that this option is enabled.
        if (!$this->asgarosforum->options['reports_enabled']) {
            return;
        }

        // Ensure that we are in the overview.
        if ($this->asgarosforum->current_view !== 'overview') {
            return;
        }

        // Ensure that the current user is at least a moderator.
        if (!$this->asgarosforum->permissions->isModerator('current')) {
            return;
        }

        // Ensure that we are not already inside the reports view.
        if ($this->asgarosforum->current_view === 'reports') {
            return;
        }

        $reports_counter = $this->count_reports();

        if ($reports_counter > 0) {
            echo '<div class="reports-notice">';
                echo '<span class="dashicons-before dashicons-warning">';
                    echo '<a href="'.$this->asgarosforum->rewrite->get_link('reports').'">'.__('There are reports.', 'asgaros-forum').'</a>';
                echo '</span>';
            echo '</div>';
        }
    }

    public function show_reports() {
        $reports = $this->get_reports();

        echo '<div class="title-element"></div>';

        if (empty($reports)) {
            echo '<div class="notice">'.__('There are no reports.', 'asgaros-forum').'</div>';
        } else {
            foreach ($reports as $post_id => $reporter_ids) {
                $report = $this->get_report($post_id, $reporter_ids);

                echo '<div class="report-element">';
                    $post_link = '<a href="'.$report['post_link'].'" title="'.$report['topic_name'].'">'.$report['topic_name'].'</a>';
                    $post_author = $this->asgarosforum->getUsername($report['author_id']);

                    echo '<div class="report-source">';
                        echo sprintf('Posted in %s by %s', $post_link, $post_author);
                        echo '&nbsp;&middot;&nbsp;';
                        echo __('Reported by:', 'asgaros-forum').'&nbsp;';

                        $first_reporter = true;

                        foreach ($report['reporters'] as $reporter) {
                            $userdata = get_userdata($reporter);

                            if (!$first_reporter) {
                                echo ',&nbsp;';
                            }

                            echo $this->asgarosforum->getUsername($reporter);

                            $first_reporter = false;
                        }
                    echo '</div>';

                    echo '<div class="report-content">';
                        if (strlen($report['post_text']) > 300) {
                            $report['post_text'] = mb_substr($report['post_text'], 0, 300, 'UTF-8') . ' &hellip;';
                        }

                        echo $report['post_text'];
                    echo '</div>';

                    echo '<div class="report-actions">';
                        $delete_link = $this->asgarosforum->rewrite->get_link('reports', false, array('report_delete' => $report['post_id']));

                        echo '<a class="dashicons-before dashicons-trash" href="'.$delete_link.'">'.__('Delete Report', 'asgaros-forum').'</a>';
                        echo '<a class="dashicons-before dashicons-visibility" href="'.$report['post_link'].'">'.__('Show Post', 'asgaros-forum').'</a>';
                    echo '</div>';
                echo '</div>';
            }
        }
    }
}
