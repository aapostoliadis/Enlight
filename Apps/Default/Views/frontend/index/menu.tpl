<div class="topbar">
  <div class="fill">
    <div class="container">
      <a class="brand" href="#">Enlight</a>
      <ul class="nav">
        <li class="active"><a href="{url action=index}">Home</a></li>
        <li><a href="{url action=login}">Form</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
        <form action="{url controller=search}" class="pull-left">
          <input type="text" placeholder="Search">
        </form>
        <form action="" class="pull-right">
            <select id="locale-select" name="__locale" class="auto-submit">
                <option>{* $Site->Locale() *}</option>
                <option>de_DE</option>
                <option>en_GB</option>
                <option>fr_FR</option>
            </select>
        </form>
    </div>
  </div>
</div>