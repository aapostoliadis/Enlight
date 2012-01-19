<div class="topbar">
  <div class="fill">
    <div class="container">
      <a class="brand" href="{url action=index}">Enlight</a>
      <ul class="nav">
        <li {if $activeMenu=="index"}class="active"{/if}><a href="{url action=index}">Home</a></li>
        <li {if $activeMenu=="listing"}class="active"{/if}><a href="{url controller=listing action=index}">All Posts</a></li>
      </ul>
        <form action="{url controller=search}" class="pull-right" method="post">
          <input type="text" name="searchTerm" placeholder="Search">
        </form>
    </div>
  </div>
</div>