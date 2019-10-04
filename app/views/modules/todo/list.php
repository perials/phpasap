<div class="container">
    <h3 class="mt-5">All Todos <small><a class="badge badge-success" href="<?php echo $this->html->url('todo/add'); ?>">Add new</a></small></h3>
    <?php if($this->session->get("success")) { ?>
        <div class="alert alert-success">
            <div><?php echo $this->session->get("success"); ?></div>
        </div>
    <?php } ?>
    <?php if($this->session->get("errors")) { ?>
        <div class="alert alert-danger">
            <?php foreach($this->session->get("errors") as $error) { ?>
                <div><?php echo $error; ?></div>
            <?php } ?>
        </div>
    <?php } ?>
    <table class="table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Title</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($todos as $todo) { ?>
                <tr>
                    <td><?php echo $todo->id; ?></td>
                    <td><?php echo $todo->title; ?></td>
                    <td><?php echo $todo->due_date; ?></td>
                    <td>
                        <a class="btn btn-light border" href="<?php echo $this->html->url("todo/edit/" . $todo->id); ?>">Edit</a>
                        <form class="d-inline" action="<?php echo $this->html->url("todo/delete/" . $todo->id); ?>" method="post">
                            <button class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            <?php if(empty($todos)) { ?>
                <tr>
                    <td colspan="4">
                        No Todos found!! Click <?php echo $this->html->link("todo/add", "here"); ?> to add one.
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>