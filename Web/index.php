<html>
<head>
<title>GawdScape High Scores</title>
</head>
<body>
<h1>GawdScape High Scores</h1>
<h3>Top List</h3>
<form action="top.php" method="get">
    <select name="do">
        <option value="achievement">Achievements</option>
        <option value="stat">General Stats</option>
        <option value="stat.mineBlock">Blocks Mined</option>
        <option value="stat.breakItem">Items Broke</option>
        <option value="stat.craftItem">Items Crafted</option>
        <option value="stat.useItem">Items Used</option>
        <option value="stat.killEntity">Mobs Killed</option>
        <option value="stat.entityKilledBy">Mobs Killed By</option>
    </select><br>
    <button type="submit">Go</button>
</form>
<h3>Compare Stats</h3>
<form action="compare.php" method="get">
    <select name="do">
        <option value="achievement">Achievements</option>
        <option value="stat">General Stats</option>
        <option value="stat.mineBlock">Blocks Mined</option>
        <option value="stat.breakItem">Items Broke</option>
        <option value="stat.craftItem">Items Crafted</option>
        <option value="stat.useItem">Items Used</option>
        <option value="stat.killEntity">Mobs Killed</option>
        <option value="stat.entityKilledBy">Mobs Killed By</option>
    </select><br>
    <b>Player 1: </b><input type="text" name="player1"><br>
    Player 2: <input type="text" name="player2"><br>
    <button type="submit">Go</button>
</form>
<br><br>
<a href="http://www.gawdscape.com">GawdScape Homepage</a>
</body>
</html>