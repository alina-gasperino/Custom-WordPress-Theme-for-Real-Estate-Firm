<?php if ($files = project_googledrive_files(get_the_ID())): ?>
    <div class="card googledrive grid">
        <div class="card__title">Downloads Now Available</div>
        <div class="card__content">
            <div class="iframe__container">
                <div class="docs">
                    <?php foreach ($files as $file): ?>
                        <div class="doc" style="border: 1px solid lightgray; border-radius: 8px; box-shadow: rgb(0 0 0 / 24%) 0px 3px 8px;">
                            <a href="https://drive.google.com/file/d/<?= $file->id ?>/view" target="_blank" onerror="this.src='https://drive-thirdparty.googleusercontent.com/16/type/application/pdf'">
                                <!--					<img class="doc__thumbnail" src="-->
                                <?php //echo $file->thumbnailLink ?><!--">-->
                                <img class="doc__thumbnail"
                                     src="https://drive.google.com/thumbnail?id=<?= $file->id ?>">
                                <span class="doc__title"><?php echo trim_title($file->name, 24) ?></span>
                            </a>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>
