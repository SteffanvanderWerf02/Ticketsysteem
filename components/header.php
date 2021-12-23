<header>
    <div class="container">
        <div class="row">
            <div class="col-lg-1">
                <a href="../pages/ticket_overview.php"><img src="../assets/img/logo/newLogoWhite.svg" alt="Logo"></a>
            </div>
            <div class="col-lg-11 my-auto">
                <nav>
                    <ul class="nav">
                        <li class="my-auto">
                            <a href="../pages/issue_overview.php">Nieuw issue</a>
                        </li>
                        <li class="my-auto">
                            <a href="../pages/ticket_overview.php">Ticket overzicht</a>
                        </li>
                        <li class="my-auto">
                            <a href="../pages/diensten_overview.php">Dienst/Service overzicht</a>
                        </li>
                        <li class="my-auto">
                            <a href="../pages/product_overview.php">Product aanvraag overzicht</a>
                        </li>
                        <li class="my-auto">
                            <form class="mb-0" method="POST" action="../index.php">
                                <button class="btn btn-clear" type="submit" name="logout">Uitloggen</button>
                            </form>
                        </li>
                        <li class="my-auto">
                            <a href="../pages/profile_edit.php">
                                <?php
                                $stmt = mysqli_prepare($db, "
                                    SELECT  profilepicture
                                    FROM    user
                                    WHERE   user_id = ?
                                ") or die(mysqli_error($db));
                                mysqli_stmt_bind_param($stmt, "i", $_SESSION["userId"]);
                                mysqli_stmt_execute($stmt) or die(mysqli_error($db));
                                mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
                                if (mysqli_stmt_num_rows($stmt) > 0) {
                                    mysqli_stmt_bind_result($stmt, $profilePicture);
                                    mysqli_stmt_fetch($stmt);
                                    mysqli_stmt_close($stmt);
                                } else {
                                    echo "geen data";
                                }

                                if ($profilePicture == NULL) {
                                    echo "<span class='material-icons align-middle'>person</span>";
                                } else {
                                    if(OS){
                                        echo "<div class='profilepic' style='background-image: url(..$profilePicture);'></div>";
                                    } else{
                                       echo "<div class='profilepic' style='background-image: url($profilePicture);'></div>";
                                    }
                                    
                                }
                                ?>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>