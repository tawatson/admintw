$(function() {

  $(".repoUpdate").click(function() {
     repoid = "#repo-"+$(this).data("repo");
      $(repoid).html("<i class='fa fa-refresh fa-spin'></i>");
      $.post( "/classes/ajax.php", { action: "git_pull", repo: $(this).data("repo")},function(data) {
        if(data) {
          $(repoid).removeClass("table-warning");
          $(repoid).addClass("table-success");
          $(repoid).html("Up to date.");
          console.log("Pull Successful");
        } else {
          new PNotify({
              title: 'Uh Oh!',
              text: 'Something terrible happened.',
              type: 'error',
              styling: 'bootstrap3'
          });
          $(repoid+ " > a > i").removeClass("fa-spin");
          console.log("Error while pulling");
        }
      });
    });


});