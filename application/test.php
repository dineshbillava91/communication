   
<!-- Region fields are available in this template -->
<h5>{{#if title}} {{title}} {{else}} {{id}} {{/if}}</h5>
 


<!-- Show all linked Database Objects: -->
{{#each objects}}

  <!-- DB Object are available inside of this block -->

  <h5>{{title}}</h5>
  <!-- When you need to render a field as HTML, use 3 curly braces instead of 2:-->
  <p>{{{description}}}</p>
  <p><em>{{location.address.formatted}}</em></p>
 

  <!-- Show all images: -->
  {{#each images}}
    <!-- Image fields "thumbnail", "medium", "full" -->
    <!-- are available in this block                -->
<div class="img-block">   <img src="{{thumbnail}}" /> </div>
  {{/each}}  

9738200523
<div><button>check availability</button></div>

Load last session
Lazy load
Last login
Last message
Load messages from the joined time

docker run -d -v /var/www/html/communication:/var/www/html/communication -v /opt/sonarqube/data:/opt/sonarqube/data -v /opt/sonarqube/extensions:/opt/sonarqube/extensions -v /opt/sonarqube/logs:/opt/sonarqube/logs --name MFS_SonarQubeContainer -p 9005:9000 sobhanthakur/sonarqubescanner


{{/each}}

#008acb

344672191

369183033

function (mapsvg){
  //var popoverjQueryObject = this;
  var source = mapsvg.selected_id[0];
  var sourcePostion = document.querySelector('#'+source).getBoundingClientRect();
  var destinationPosition = document.querySelector('.mapsvg-popover').getBoundingClientRect();
  
  var lineID = "line-"+source;
  document.getElementById(source).innerHTML = "<svg><line id='"+lineID+"'/></svg>";

  document.getElementById(lineID).setAttribute('x1',sourcePostion.left);
  document.getElementById(lineID).setAttribute('y1',sourcePostion.top);
  document.getElementById(lineID).setAttribute('x2',destinationPosition.left);
  document.getElementById(lineID).setAttribute('y2',destinationPosition.top);
}

