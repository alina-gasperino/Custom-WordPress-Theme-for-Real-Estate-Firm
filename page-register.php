<?php

get_header();


$project_slug = get_query_var('project_slug');
$project = get_page_by_path( $project_slug, OBJECT, 'project');
global $post;
$post = get_post($project->ID);
?>
<meta name="robots" content="noindex">
<script>document.title = "<?php echo get_the_title().' Register' ?>";</script>
<style>
    #header{
        /*display: none;*/
    }
    #footer{
        display: none;
    }
    .flex-col.first{
        min-width: 360px;
    }
    .infusionsoft-form{
        min-width: 300px;
        min-height: 550px;
    }
    .content-header{
        text-align: center;
        margin-bottom: 30px;
    }
    .content-header >div{
        color: #9a9a9a;
    }
</style>

<div class="content-header">
    <h2><?=$project->post_title?></h2>
    <div>Complete the form to get immediate access to Plans & Prices</div>
</div>

<main class="site-wrapper" itemprop="mainContentOfPage" role="main">
    <div class="container">
        <article id="project-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/ApartmentComplex">
            <div class="project-content">
                <div class="tab-content">
                    <div id="overview" role="tabpanel" class="tab-pane fade in active">
                        <div class="infusionsoft-form" id = 'project-save'>
                            <section class="av_textblock_section">
                                <?php the_field( 'infusionsoftform' ); ?>
                            </section>
                        </div>
                        <?php get_template_part( 'templates/project/template-project-gallery' ); ?>

                        </div>
                    </div>
                </div>
            </div>
        </article>
    </div>
</main>

<?php get_footer(); ?>
