



function setUpCarousel(carouselnummber)
{
   var carousel =document.getElementById('carousel'+carouselnummber);
   var rotatebox = searchchildwithname(carousel,'rotatebox',2);
   
   
   totalwidth = getTotalRotateElementWidth(carouselnummber);
   
   rotatebox.style.width = totalwidth + "px"; // das kann safari erst nach ein paar MS :(
   
   rotateboxtarget[carouselnummber] = 'kein';
   rotateboxspeed[carouselnummber] = 40;
   
   var speed = carousel.getAttribute('carousel_speed');
   if (speed != null && speed != '')
   {
      rotateboxpausedauer[carouselnummber] = speed;
   }else
   {
      rotateboxpausedauer[carouselnummber] = 6000;
   }
    rotateboxbildergeladen[carouselnummber] = 0;

   markiereSelektor(carouselnummber);

   sleep(rotateboxpausedauer[carouselnummber], carouselnummber);
      
   
}

function loadimgs(carouselnummber)
{
   var carousel =document.getElementById('carousel'+carouselnummber);

   var rotatebox = searchchildwithname(carousel,'rotatebox',2);

    var children = rotatebox.childNodes;
    for (i=0;i < children.length;i++)
    {
       if (children[i].getAttribute && typeof(children[i].getAttribute('name')) != "undefined")
       {
          name = children[i].getAttribute('name');
          subname = name.substr(0,name.length-1);
          subname2 = name.substr(0,name.length-2);          
          number = name.substr(name.length-1,1);

          if (subname == 'rotatebox' || subname2 == 'rotatebox')
          {
             Bild01 = new Image();
             bild = children[i].getAttribute('imageurl');
             Bild01.src = 'url('+bild+')';
             img = "url("+bild+")";
             children[i].style.backgroundImage = img;
             rotateboxbildergeladen[carouselnummber]++;;
             
          }
       }
    }
}


function carouselFocus(carouselnummber,elementSelectId)
{
   rotateboxtarget[carouselnummber] = elementSelectId;
   
}


function carouselSlideLeft(carouselnumber)
{
      var carousel =document.getElementById('carousel'+carouselnumber);
      var carousel_gesamtelemente = parseInt(carousel.getAttribute('carousel_gesamtelemente'));


      el = getAktuellesFokuselement(carouselnumber);

      if (el > 0)
         rotateboxtarget[carouselnumber] = el -1;
      else
         rotateboxtarget[carouselnumber] = carousel_gesamtelemente-1; // ende

}

function carouselSlideRight(carouselnumber)
{
   var carousel =document.getElementById('carousel'+carouselnumber);
   var carousel_gesamtelemente = parseInt(carousel.getAttribute('carousel_gesamtelemente'));


   el = getAktuellesFokuselement(carouselnumber);

   
   if (el < carousel_gesamtelemente-1)
      rotateboxtarget[carouselnumber] = el +1;
   else
      rotateboxtarget[carouselnumber] = 0; // ende

}



function getTotalRotateElementWidth(carouselnummber)
{
    var carousel =document.getElementById('carousel'+carouselnummber);


    var rotatebox = searchchildwithname(carousel,'rotatebox',2);

    var totalwidth = 0;

    var children = rotatebox.childNodes;
    for (i=0;i < children.length;i++)
    {
       if (children[i].getAttribute && typeof(children[i].getAttribute('name')) != "undefined")
       {
          name = children[i].getAttribute('name');
          subname = name.substr(0,name.length-1);
          subname2 = name.substr(0,name.length-2);

          if (subname == 'rotatebox' || subname2 == 'rotatebox')
          {
             
             totalwidth += children[i].clientWidth;
          }
       }
    }
   return totalwidth;
}


function getTotalDivSlideElements(carouselnumber)
{
   
   var carousel =document.getElementById('carousel'+carouselnumber);

   var anzeigeelemente = parseInt(carousel.getAttribute('anzeigeelemente'));
   var carousel_gesamtelemente = parseInt(carousel.getAttribute('carousel_gesamtelemente'));

   return anzeigeelemente + carousel_gesamtelemente;
}

function rotateit()
{
   
   carouselnummber = arguments[0];
   
   if (rotateboxbildergeladen[carouselnummber] == 0)
   {
      setTimeout('rotateit('+carouselnummber+');',100);
      
   } else
   {
   
      var carousel =document.getElementById('carousel'+carouselnummber);

      var rotatebox = searchchildwithname(carousel,'rotatebox',2);
   
      var anzeigeelemente = parseInt(carousel.getAttribute('anzeigeelemente'));

      var carousel_gesamtelemente = parseInt(carousel.getAttribute('carousel_gesamtelemente'));


      scrollto = 0;
      speed = rotateboxspeed[carouselnummber];
      target = 'kein'; // um schnell irgendwo hinzuscrollen
   
      if (rotateboxtarget[carouselnummber] != 'kein')
      {
         target = rotateboxtarget[carouselnummber];
         speed *= 2;
      }
      
      var jump = false;
   
      var totalwidth = 0;
      var scrolltarget = 0;


      var divelemente = carousel_gesamtelemente + anzeigeelemente;

      totalwidth = getTotalRotateElementWidth(carouselnummber);
      singlewidth = totalwidth/divelemente;
   
       posalt = valueFromStyleInPx(rotatebox.style.marginLeft);
       
       aktuellesdiv = getAktuellesDivFokuselement(carouselnummber);

       
       direction = 'right';
       
       if (target != 'kein')
       {
          if (aktuellesdiv == 0 // ganz links
              && target > Math.floor(carousel_gesamtelemente/2) // ziel in 2. haelfte
              || (aktuellesdiv > carousel_gesamtelemente-1 && target < aktuellesdiv && target != 1) // schon ruebergesprungen
                )
          {
              direction = 'left';
          }
          
          scrollto = -singlewidth * target;              
          
          if (aktuellesdiv == carousel_gesamtelemente -1 && target == 0) // vom letzten zum 1.
          {
              direction = 'right';//redundant
              scrollto = -singlewidth * carousel_gesamtelemente;
          }
          
          
          delta = posalt - scrollto;
          if (Math.abs(delta) < speed)
          {
             speed = Math.abs(delta);
          }
       
       }

       posneu = 0;
       delta = (posalt -( -totalwidth + singlewidth));

       
       if (delta > singlewidth)
         delta = delta % singlewidth;
    
    
      var currentTime = new Date();
    
    
      jumpleft = false;
    
    
       if (delta != 0 && delta < speed) // ok
       { // move last pixels
          posneu = posalt - delta;
       } else if (delta == 0 && target != 'kein' && direction=='left' && posalt != -(carousel_gesamtelemente*singlewidth))
       {
           posneu = -(carousel_gesamtelemente*singlewidth);
           jumpleft = true;
           
       } else if (posalt == -totalwidth + (singlewidth*anzeigeelemente) && direction == 'right')
       {
          posneu = 0;
          jump = true;
       } else
       {
          if (target == 'kein' || posalt > scrollto)
             posneu = posalt - speed;
          else
            posneu = parseInt(posalt) +  parseInt(speed);
       }
    
       
       posstep = posneu % singlewidth;
    
    
      
     imFocus = getAktuellesFokuselement(carouselnummber); // temporaer zum testen
    
    
       forcepause = false;
       
       if (posneu == scrollto && target != 'kein')
        {
           rotateboxtarget[carouselnummber] = 'kein';
          if (aktuellesdiv == carousel_gesamtelemente -1 && target == 0) // vom letzten zum 1.
          {
          // forcepause = false;
          } else
          {
            //  forcepause = true;
          }
                        forcepause = true;
        }
    
      
        
        rotatebox.style.marginLeft = posneu + 'px'; 
        rotatebox.style.left = posneu +'px'; 
    

       if (jumpleft == false 
            && (posstep ==0 && ( posneu != 0 || jump == false) && target == 'kein' || forcepause))
       {
          markiereSelektor(carouselnummber);
          sleep(rotateboxpausedauer[carouselnummber],carouselnummber);
       } else
       {
          setTimeout('rotateit('+carouselnummber+');',40);
       }
   }
}  


function getAktuellesFokuselement(carouselnumber)
{
   var carousel =document.getElementById('carousel'+carouselnumber);
   var rotatebox = searchchildwithname(carousel,'rotatebox',2);
   
   totalwidth = getTotalRotateElementWidth(carouselnumber);
   
   var carousel_gesamtelemente = parseInt(carousel.getAttribute('carousel_gesamtelemente'));
   
   posalt = valueFromStyleInPx(rotatebox.style.marginLeft);
   
   elements = getTotalDivSlideElements(carouselnumber);
   
   singlewidth = totalwidth/elements;
   
    selektorid = Math.floor(-posalt/singlewidth);
 
    if (selektorid > carousel_gesamtelemente-1)
       selektorid = 0;
       
   return selektorid;
}


function getAktuellesDivFokuselement(carouselnumber)
{
   var carousel =document.getElementById('carousel'+carouselnumber);
   var rotatebox = searchchildwithname(carousel,'rotatebox',2);
   
   totalwidth = getTotalRotateElementWidth(carouselnumber);
   
   var carousel_gesamtelemente = parseInt(carousel.getAttribute('carousel_gesamtelemente'));
   
   posalt = valueFromStyleInPx(rotatebox.style.marginLeft);
   
   elements = getTotalDivSlideElements(carouselnumber);
   
   singlewidth = totalwidth/elements;
   
   selektorid = Math.floor(-posalt/singlewidth);
 
    
   return selektorid;
}


function markiereSelektor(carouselnummer)
{
   var carousel =document.getElementById('carousel'+carouselnummer);
   
   selektor = searchchildwithname(carousel,'rotateselektor',3);
   
   nummer = getAktuellesFokuselement(carouselnummer);
   
   if (selektor != null)
   {
   
      var children = selektor.childNodes;
   
      for (i=0;i < children.length;i++)
      {
         if (children[i].getAttribute && typeof(children[i].getAttribute('name')) != "undefined")
         {
            name = children[i].getAttribute('name');
      
            subname = name.substr(0,name.length-1);
            subname2 = name.substr(0,name.length-2);
            subid = name.substr(name.length-1,1);
      
            if (subname == 'rotatebutton' || subname2 == 'rotatebutton')
            {
               if (subid == nummer)
               {
                  children[i].style.opacity = 1;
                  children[i].style.filter  = "alpha(opacity=" + 100 + ")";
               }else
               {
                  children[i].style.opacity = 0.5;
                  children[i].style.filter  = "alpha(opacity=" + 50 + ")";
               }
            } else
            {
               
            }
         }
 
      
      }
   }
}

function searchchildwithname(fatherobj, childname,tiefe)
{
   
   if (tiefe <= 0)
      return null;

   var children = fatherobj.childNodes;

   for (i=0;i < children.length;i++)
   {
      
      if (children[i].getAttribute && typeof(children[i].getAttribute('name')) != "undefined")
      {
         if (children[i].getAttribute('name') == childname)
            return children[i];
      }
      
      
   }
   
   for (i=0;i < children.length;i++)
     {
        if (children[i].childNodes.length != 0)
        {
           child = searchchildwithname(children[i], childname, tiefe-1);
           if (child != null)
           {
              return child;
           }
        }

     }
   return null;
}

function sleep(ms, carouselnummer)
{
   if (ms <= 0 || rotateboxtarget[carouselnummer] != 'kein')
   {
      rotateit(carouselnummer);;
   	
   } else
   {
      setTimeout('sleep('+(ms-200)+','+carouselnummer+');',200);
   	
   }
   
}

function valueFromStyleInPx(text) // 200px => 200
{
   return text.substr(0,text.length - 2);
}
 
   

-->

