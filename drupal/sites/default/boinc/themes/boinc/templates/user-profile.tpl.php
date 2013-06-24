<?php
// $Id: user-profile.tpl.php,v 1.2.2.2 2009/10/06 11:50:06 goba Exp $

/**
 * @file user-profile.tpl.php
 * Default theme implementation to present all user profile data.
 *
 * This template is used when viewing a registered member's profile page,
 * e.g., example.com/user/123. 123 being the users ID.
 *
 * By default, all user profile data is printed out with the $user_profile
 * variable. If there is a need to break it up you can use $profile instead.
 * It is keyed to the name of each category or other data attached to the
 * account. If it is a category it will contain all the profile items. By
 * default $profile['summary'] is provided which contains data on the user's
 * history. Other data can be included by modules. $profile['user_picture'] is
 * available by default showing the account picture.
 *
 * Also keep in mind that profile items and their categories can be defined by
 * site administrators. They are also available within $profile. For example,
 * if a site is configured with a category of "contact" with
 * fields for of addresses, phone numbers and other related info, then doing a
 * straight print of $profile['contact'] will output everything in the
 * category. This is useful for altering source order and adding custom
 * markup for the group.
 *
 * To check for all available data within $profile, use the code below.
 * @code
 *   print '<pre>'. check_plain(print_r($profile, 1)) .'</pre>';
 * @endcode
 *
 * Available variables:
 *   - $user_profile: All user profile data. Ready for print.
 *   - $profile: Keyed array of profile categories and their items or other data
 *     provided by modules.
 *
 * @see user-profile-category.tpl.php
 *   Where the html is handled for the group.
 * @see user-profile-item.tpl.php
 *   Where the html is handled for each item in the group.
 * @see template_preprocess_user_profile()
 */
?>
<?php

drupal_set_title('');
$account = user_load($account->uid);
$content_profile = content_profile_load('profile', $account->uid);
$name = check_plain($account->boincuser_name);
$join_date = date('d F Y', $account->created);
$country = check_plain($content_profile->field_country[0]['value']);
$website = check_plain($content_profile->field_url[0]['value']);
$background = $content_profile->field_background[0]['value'];
$opinions = $content_profile->field_opinions[0]['value'];
$user_links = array();

if ($user->uid AND ($user->uid != $account->uid)) {
  $user_links[] = array(
    'title' => t('Send message'),
    'href' => privatemsg_get_link(array($account))
  );
  $user_links[] = array(
    'title' => t('Add as friend'),
    'href' => "flag/confirm/flag/friend/{$account->uid}"
  );
  if (user_access('assign community member role')) {
    if (array_search('community member', $account->roles)) {
      $user_links[] = array(
        'title' => t('Ban user'),
        'href' => "user_control/{$account->uid}/ban"
      );
    }
    else {
      $user_links[] = array(
        'title' => t('Lift user ban'),
        'href' => "user_control/{$account->uid}/lift-ban"
      );
    }
  }
}
$link_index = 0;

?>
<div class="user-profile">
  <div class="picture">
    <?php 
      $user_image = boincuser_get_user_profile_image($account->uid, FALSE);
      print theme('imagefield_image', $user_image['image'], $user_image['alt'],
        $user_image['alt'], array(), false);
    ?>
  </div>
  <div class="general-info">
    <div class="name">
      <span class="label"></span>
      <span class="value"><?php print $name; ?></span>
    </div>
    <div class="join-date">
      <span class="label"><?php print t('Member since'); ?>:</span>
      <span class="value"><?php print $join_date; ?></span>
    </div>
    <div class="country">
      <span class="label"><?php print t('Country'); ?>:</span>
      <span class="value"><?php print $country; ?></span>
    </div>
    <?php if ($website): ?>
      <div class="website">
        <span class="label"><?php print t('Website'); ?>:</span>
        <span class="value"><?php print l($website, (strpos($website, 'http') === false) ? "http://{$website}" : $website); ?></span>
      </div>
    <?php endif; ?>
    <?php if ($user->uid AND ($user->uid != $account->uid)): ?>
      <ul class="tab-list">
        <?php foreach ($user_links as $link): ?>
          <li class="<?php print ($link_index == 0) ? 'first ' : ''; ?>tab<?php print ($link_index == count($user_links)-1) ? ' last' : ''; ?>">
            <?php print l($link['title'], $link['href'], array('query' => drupal_get_destination())); ?>
          </li>
        <!--<li class="first tab"><?php print l(t('Send message'), privatemsg_get_link(array($account)), array('query' => drupal_get_destination())); ?></li>
        <li class="last tab"><?php print l(t('Add as friend'), "flag/confirm/flag/friend/{$account->uid}", array('query' => drupal_get_destination())); ?></li>-->
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <div class="clearfix"></div>
  </div>
  <?php if ($background OR $opinions): ?>
    <div class="bio">
      <?php if ($background): ?>
        <div class="background">
          <span class="label"><?php print t('Background'); ?></span>
          <span class="value"><?php print $background; ?></span>
        </div>
      <?php endif; ?>
      <?php if ($opinions): ?>
        <div class="opinions">
          <span class="label"><?php print t('Opinion'); ?></span>
          <span class="value"><?php print $opinions; ?></span>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <?php /*print $user_profile; ?>
  <pre><?php print_r($profile); ?></pre>
  <pre><?php print_r($account); ?></pre>
  <pre><?php print_r($content_profile); ?></pre> */ ?>
</div>
