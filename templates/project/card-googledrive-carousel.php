<?php if ($files = project_googledrive_files(get_the_ID())): ?>

    <div class="condos condos-group pdfs-group" data-animation="fadeIn">

        <header>
            <h1 class="heading">Downloads Now Available</h1>
        </header>

        <div id="project-pdf-carousel" class="project-carousel condos-slider gscroll">
            <div class="condos-slider__scroller nonclick">
                <?php foreach ($files as $file): ?>

                    <div class="project">
                        <a href="https://drive.google.com/file/d/<?= $file->id ?>/view" target="_blank" onerror="this.src='https://drive-thirdparty.googleusercontent.com/16/type/application/pdf'">
                            <figure>
                                <img class="doc__thumbnail"src="https://drive.google.com/thumbnail?id=<?= $file->id ?>">
                            </figure>
                            <figcaption>
                                <img class="icon" src="https://drive-thirdparty.googleusercontent.com/16/type/application/pdf">
                                <h4 class="condo-title"><?= trim_title($file->name, 22) ?></h4>
                            </figcaption>
                        </a>
                    </div>

                <?php endforeach ?>
            </div>
        </div>

    </div>
<?php endif ?>
