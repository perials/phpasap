<?php
// load few classes that we'll be using
// use core\alias\HTML;
// use core\alias\Form;
// use core\alias\Session;
?>

<div class="container">
    <h3 class="mt-5 mb-3"><?php echo isset($todo) ? "Edit" : "Create"; ?> Todos <small><a class="badge badge-secondary" href="<?php echo $this->html->url('todo'); ?>">View All</a></small></h3>
    <?php if($this->session->get("errors")) { ?>
        <div class="alert alert-danger">
            <?php foreach($this->session->get("errors") as $error) { ?>
                <div><?php echo $error; ?></div>
            <?php } ?>
        </div>
    <?php } ?>
    <form action="<?php echo $this->html->url("todo/save"); ?>" method="post">
        <?php echo $this->form->input('title', $todo->title ?? null, ["placeholder"=>"Title", "class"=>"form-control mb-3"]); ?>
        <?php echo $this->form->input('due_date', $todo->due_date ?? null, ["placeholder"=>"Due date", "class"=>"form-control mb-3"]); ?>
        <?php if (isset($todo)) { ?>
            <input type="hidden" name="id" value="<?php echo $todo->id; ?>" />
        <?php } ?>
        <input type="submit" class="btn btn-primary" />
    </form>
</div>