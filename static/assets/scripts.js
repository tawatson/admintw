$(function() {
    $("main").on('click','.refreshRepos',function(e){
	     e.preventDefault();
       $(".refreshRepos .fa-refresh").addClass("fa-spin");
       $("table.table-hover").parent().load("/settings.php table.table-hover", function(){$(".refreshRepos .fa-refresh").removeClass("fa-spin");});
    });

    $("main").on('click','.repoUpdate',function(e){
      e.preventDefault();
      repoid = "#repo-"+$(this).data("repo");
       $(repoid).html("<i class='fa fa-refresh fa-spin'></i>");
       $.post( "ajax.php", { action: "git_pull", repo: $(this).data("repo")},function(data) {
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

    $("body").on('click','a',function(e){
      e.preventDefault();
      window.location=this.getAttribute("href");
      return false;
    });
});
