<!DOCTYPE html>
<html lang="fr">
<head>
    <?php $title = "Manage users"; include('head.php'); ?>
</head>
    <body  class="has-navbar-fixed-top m-4">
        <header>
            <?php include('menu.php') ?>
        </header>
        <main>
            <article>
                <section class="ml-2 mb-6">
                    <h2 class="title mb-6">Manage Users</h2>
                    <form id="add_user" action='user/add' method='post'></form>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <span class="icon-text">
                                        <span class="icon">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <span>Full name</span>
                                    </span>
                                </th>
                                <th>
                                    <span class="icon-text">
                                        <span class="icon">
                                            <i class="fas fa-at"></i>
                                        </span>
                                        <span>Email</span>
                                    </span>
                                </th>
                                <th>
                                    <span class="icon-text">
                                        <span class="icon">
                                            <i class="fas fa-user-shield"></i>
                                        </span>
                                        <span>Role</span>
                                    </span>
                                </th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($users as $member): ?>
                            <tr>
                                <td><?= $member->get_fullName() ?></td>
                                <td><?= $member->get_email() ?></td>
                                <td><?= lcfirst($member->get_role()) ?></td>
                                <td class="has-text-centered">
                                    <a class="icon" href="user/edit/<?= $member->get_id() ?>">
                                        <button class="button align-baseline p-1" type="submit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </a>
                                </td>
                                <?php if($user != $member): ?>
                                <td class="has-text-centered">
                                    <form action='user/delete' method='post'>
                                        <input type='text' name='id' value='<?= $member->get_id() ?>' hidden>
                                        <button class="button align-baseline is-align-items-start p-1" type="submit">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                <?php else: ?>
                                <td class="has-text-centered">
                                    <button class="button align-baseline p-0"disabled>
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                                <?php endif ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
                <a class="button is-info" href="user/add">Add a user</a>
            </article>
        </main>
    </body>
</html>
