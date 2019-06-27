<div class="container mt-5">
    <h3>Add Todo</h3>
    <form method="post" action="save">
        <div class="form-group">
            <label>Title</label>
            <?php echo $this->form->input('title', null, ['class' => 'form-control']); ?>
        </div>
        <div class="form-group">
            <label>Description</label>
            <?php echo $this->form->textarea('title', null, ['class' => 'form-control']); ?>
        </div>
        <input type="submit" class="btn btn-primary" />
    </form>
</div>