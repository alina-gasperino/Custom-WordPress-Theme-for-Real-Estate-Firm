<div id="popular_neighbourhoods">

      <h4><i class="fa fa-map-marker"></i> Popular Neighbourhoods</h4>

      <?php if ($popular = get_field('popular_neighbourhoods')): ?>

            <ul>
                  <?php foreach ($popular as $term): ?>
                        <li><a class='orange-label' href="<?php echo get_term_link($term) ?>"><?php echo $term->name ?></a></li>
                  <?php endforeach; ?>
            </ul>

      <?php else: ?>

            <?php $terms = get_terms( 'neighbourhood', array('')); ?>
            <?php $popular = array(
                  'Yorkville',
                  'Entertainment District',
                  'Waterfront',
                  'Mimico-Humber Bay Park',
                  'Downtown Core',
                  'Church & Wellesley',
            ) ?>
            <ul>
                  <?php foreach ($popular as $name): ?>
                        <?php $term = get_term_by('slug', sanitize_title($name), 'neighbourhood'); ?>
                        <?php if (is_object($term)): ?>
                              <?php $link = get_term_link($term); ?>
                              <li><a class='orange-label' href="<?php echo get_term_link($term) ?>"><?php echo $term->name ?></a></li>
                        <?php endif; ?>
                  <?php endforeach; ?>
            </ul>

      <?php endif; ?>

</div>
