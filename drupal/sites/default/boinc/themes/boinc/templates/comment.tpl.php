<?php
// $Id: comment.tpl.php,v 1.10 2009/11/02 17:42:27 johnalbin Exp $

/**
 * @file
 * Default theme implementation for comments.
 *
 * Available variables:
 * - $author: Comment author. Can be link or plain text.
 * - $content: Body of the comment.
 * - $created: Formatted date and time for when the comment was created.
 *   Preprocess functions can reformat it by calling format_date() with the
 *   desired parameters on the $comment->timestamp variable.
 * - $new: New comment marker.
 * - $picture: Authors picture.
 * - $signature: Authors signature.
 * - $status: Comment status. Possible values are:
 *   comment-unpublished, comment-published or comment-preview.
 * - $title: Linked title.
 * - $links: Various operational links.
 * - $unpublished: An unpublished comment visible only to administrators.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the following:
 *   - comment: The current template type, i.e., "theming hook".
 *   - comment-by-anonymous: Comment by an unregistered user.
 *   - comment-by-node-author: Comment by the author of the parent node.
 *   - comment-preview: When previewing a new or edited comment.
 *   - first: The first comment in the list of displayed comments.
 *   - last: The last comment in the list of displayed comments.
 *   - odd: An odd-numbered comment in the list of displayed comments.
 *   - even: An even-numbered comment in the list of displayed comments.
 *   The following applies only to viewers who are registered users:
 *   - comment-by-viewer: Comment by the user currently viewing the page.
 *   - comment-unpublished: An unpublished comment visible only to administrators.
 *   - comment-new: New comment since the last visit.
 *
 * These two variables are provided for context:
 * - $comment: Full comment object.
 * - $node: Node object the comments are attached to.
 *
 * Other variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 *
 * The following variables are deprecated and will be removed in Drupal 7:
 * - $date: Formatted date and time for when the comment was created.
 * - $submitted: By line with date and time.
 *
 * @see template_preprocess()
 * @see template_preprocess_comment()
 * @see zen_preprocess()
 * @see zen_preprocess_comment()
 * @see zen_process()
 */
?>
<div class="<?php print $classes; ?> clearfix">
  <?php
    static $authors;
    if (_ignore_user_ignored_user($comment->uid)) {
      if (!$authors[$comment->uid]) {
        $authors[$comment->uid] = user_load(array('uid' => $comment->uid));
      }
      // Remove the wrapper the Ignore User module puts around node->content.
      // It should be around the whole comment, not one part...
      // Absurd nested functions to remove wrappers are as follows.
      $content = strstr(end(explode('<div class="ignore-user-content">', $content)), '</div></div>', TRUE);
      print '<div class="ignore-user-container">';
      print t('!username is on your !ignore_list. Click !here to view this post.',
        array(
          '!username' => theme('username', $authors[$comment->uid]),
          '!ignore_list' => l(t('ignore list'), 'ignore_user/list'),
          '!here' => l(t('here'), "node/{$comment->nid}#comment-{$comment->cid}",
            array('attributes' => array('class' => 'ignore-user-content-link')))
        )
      );
      print '<div class="ignore-user-content">';
    } 
    ?>
  <div class="user">
    <?php
      $account = user_load(array('uid' => $comment->uid));
      $user_image = boincuser_get_user_profile_image($comment->uid);
      if ($user_image['image']['filepath']) {
        print '<div class="picture">';
        //print theme('imagecache', 'thumbnail', $user_image['image']['filepath'], $user_image['alt'], $user_image['alt']);
        print theme('imagefield_image', $user_image['image'], $user_image['alt'], $user_image['alt'], array(), false);
        print '</div>';
      }
    ?>
    <div class="name"><?php print $author; ?></div>
    <?php if ($account->uid): ?>
      <div class="join-date">Joined: <?php print date('j M y', $account->created); ?></div>
      <div class="post-count">Posts: <?php print $account->post_count; ?></div>
      <div class="credit">Credit: <?php print $account->boincuser_total_credit; ?></div>
      <div class="rac">RAC: <?php print $account->boincuser_expavg_credit; ?></div>
      <?php if ($account->uid): ?>
        <div class="pm-link"><?php print l(t('Send message'),
          privatemsg_get_link(array($account)),
          array('query' => drupal_get_destination())); ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <div class="comment-body">
    <?php if ($title): ?>
      <h3 class="title">
        <?php print $title; ?>
        <?php if ($new): ?>
          <span class="new"><?php print $new; ?></span>
        <?php endif; ?>
      </h3>
    <?php elseif ($new): ?>
      <div class="new"><?php print $new; ?></div>
    <?php endif; ?>

    <?php if ($unpublished): ?>
      <div class="unpublished"><?php print t('Unpublished'); ?></div>
    <?php endif; ?>

    <div class="submitted">
      <?php print date('j M Y H:i:s T', $comment->timestamp); ?>
    </div>
    <div class="comment-id">
      <?php echo l(t('Message @id', array('@id' => $comment->cid)),
        "node/{$comment->nid}", 
        array('fragment' => "comment-{$comment->cid}")); ?>
      <?php 
        if ($comment->pid):
          $parent = _comment_load($comment->pid);
          if ($parent->status == COMMENT_PUBLISHED) {
            $parent_link = l(t('message @id', array('@id' => $comment->pid)),
            "node/{$comment->nid}", array('fragment' => "comment-{$comment->pid}"));
          }
          else {
            $parent_link = '(' . t('parent removed') . ')';
          }
          echo t(' in response to !parent', array(
            '!parent' => $parent_link
          ));
        endif;
      ?>
    </div>

    <div class="content">
      <?php print $content; ?>
      <?php if ($signature): ?>
        <div class="user-signature clearfix">
          <?php print $signature; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php print $links; ?>
  </div> <!-- /.comment-body -->
</div> <!-- /.comment -->
