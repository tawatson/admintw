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

    // Mobile Safari in standalone mode
if(("standalone" in window.navigator) && window.navigator.standalone){

	// If you want to prevent remote links in standalone web apps opening Mobile Safari, change 'remotes' to true
	var noddy, remotes = false;

	document.addEventListener('click', function(event) {

		noddy = event.target;

		// Bubble up until we hit link or top HTML element. Warning: BODY element is not compulsory so better to stop on HTML
		while(noddy.nodeName !== "A" && noddy.nodeName !== "HTML") {
	        noddy = noddy.parentNode;
	    }

		if('href' in noddy && noddy.href.indexOf('http') !== -1 && (noddy.href.indexOf(document.location.host) !== -1 || remotes))
		{
			event.preventDefault();
			document.location.href = noddy.href;
		}

	},false);
}

if(("standalone" in window.navigator) && window.navigator.standalone){
  alert("Standalone!")
} else {
  alert("Web Version!")
}
});
