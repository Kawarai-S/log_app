<div class="menu">
    <ul>
        <li><a href="top.php?id=<?=h($target_id)?>"><i class="fa-solid fa-paw"></i><span>Pets</span></a></li>
        <li><a href="calendar.php?id=<?=h($target_id)?>"><i class="fa-solid fa-calendar-days"></i><span>Calendar</span></a></li>
        <li><a href="hospital_serch.php?id=<?=h($target_id)?>"><i class="fa-solid fa-stethoscope"></i><span>Hospital</span></a></li>
        <li><a href="#"><i class="fas fa-user"></i><span>Profile</span></a></li>
        <li>
            <form method="post" action="logout.php">
                <button type="submit" name="logout" class="logout_btn">
                    <i class="fa-solid fa-right-from-bracket"></i><span>Log out</span>
                </button>
            </form>
        </li>
    </ul>
</div>