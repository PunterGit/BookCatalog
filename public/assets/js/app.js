let cancel = '';

function startLoadingAnimation() {
   let imgObj = $("#loadImg");
   imgObj.show();
   let centerY = $(window).scrollTop() + ($(window).height() + imgObj.height())/2;
   let centerX = $(window).scrollLeft() + ($(window).width() + imgObj.width())/2;

   imgObj.offset({top:centerY, left:centerX});
}

function stopLoadingAnimation(){
   $("#loadImg").hide();
}

function warningEmptyAuthorOrGenre() {
   $('<div>Введите как минимум один жанр и одного автора</div>').dialog({
      title: 'У книги должен быть как минимум один жанр и один автор!',
      resizable: false,
      modal: true,
      show: 'slide',
      draggable: false,
      buttons: {
         'Понятно': function()  {
            $(this).dialog( 'close' );
         }
      }
   });
}

function deleteFromDisplay (element) {
   if (element.next().attr('name')===''){
      element.next().remove();
      element.remove();
   }else {
      element.next().val('DELETE');
      element.next().css('display','none');
      element.remove();
   }
}

function bookParse (book){
   let header = '<form><div class="row" id="'+book['id']+'"><div class="col-1"><img src="/assets/img/Cancel.png" alt="Отменить"><img src="/assets/img/AddBook.png" alt="Добавить книгу" class="sideBtn"><img src="/assets/img/RemoveBook.png" alt="Удалить" class="sideBtn"></div><div class="col-4"><input class="form-control" type="text" maxlength="100" name="'+book['title']+'" placeholder="Название книги" value="'+book['title']+'"><p>'+book['title']+'</p></div><div class="col-2">';
   $(book['genres']).each(function (index, value) {
      if(value !== "DELETE"){
         header += '<img src="/assets/img/Minus.png" alt="Удалить жанр" class="minIcons"><input class="genreInput form-control" type="text" maxlength="100" name="'+value+'" value="'+value+'" placeholder="Жанр"><p>'+value+'</p>';
      }
   });
   header += '<span class="add"><img src="/assets/img/Add.png" alt="Добавить" class="minIcons"> Добавить жанр</span></div><div class="col-2">';
   $(book['authors']).each(function (index, value) {
      if( value !== "DELETE") {
         header += '<img src="/assets/img/Minus.png" alt="Удалить автора" class="minIcons"><input class="authorInput form-control" type="text" maxlength="100" placeholder="Автор" name="' + value + '" value="' + value + '"><p>' + value + '</p>';
      }
   });
   header += '<span class="add"><img src="/assets/img/Add.png" alt="Добавить" class="minIcons"> Добавить автора</span></div><div class="col-2"><input class="form-control" type="number" max="2019" min="1000" pattern="(^[1-2])(\\d)" placeholder="2019" name="'+book['pubDate']+'" value="'+book['pubDate']+'"><p>'+book['pubDate']+'</p></div><div class="col-1"><img src="/assets/img/Edit.png" alt="Изменить" class="sideBtn"><img src="/assets/img/Save.png" alt="Сохранить" class="sideBtn" style="display: none"></div></div></form>';
   return header;
}

function bookInfo (book){
   let title; let pubDate;
   let genres = []; let authors = [];
   let id; let validation = true;
   id = book.parents(".row").attr("id");
   title = book.parents(".row").children('.col-4').children('input').val();
   pubDate = book.parents(".row").children('.col-2').children('[type="number"]').val();
   book.parents(".row").children('.col-2').children('.genreInput').each(function (index, value) {
      validation = (validation === true) ? ($(value).val() === "DELETE"):false;
      genres.push($(value).val());
   });
   if(!validation){
      validation = !validation;
      book.parents(".row").children('.col-2').children('.authorInput').each(function (index, value) {
         validation = (validation === true) ? ($(value).val() === "DELETE"):false;
         authors.push($(value).val());
      });
   }
   return {"id":id, "title":title, "pubDate":pubDate, "genres":genres, "authors":authors, "validation":validation};
}

$('.content').css("padding-top",$('.navbar').height()+10);
$(window).on('resize', function (){
   $('.content').css("padding-top",$('.navbar').height()+10);
});

function updateAutocomplete(){
   $.ajax({
      url: '/genre/list/',
      type: 'GET',
      dataType: 'json',
      success: function (response) {
         let genresAC = response[0];
         let authorsAC = response[1];
         $( ".genreInput" ).autocomplete({
            source: genresAC
         });
         $( ".authorInput" ).autocomplete({
            source: authorsAC
         });
      }
   });
}

updateAutocomplete();

$(document).on("click",'[alt="Изменить"]',function () {
   $('body').removeClass('lockHover');
   cancel = $(this).parents(".row").clone();
   $(this).css("display","none");
   $(this).siblings().css("display","inline-block");
   $(this).parents(".row").children(".col-4").children("p").css("display","none");
   $(this).parents(".row").children(".col-4").children("input").css("display","inline-block");
   $(this).parents(".row").children(".col-2").children("p").css("display","none");
   $(this).parents(".row").children(".col-2").children("input").css("display","inline-block");
   $(this).parents(".row").children(".col-1").children('[alt="Отменить"]').css("display","inline-block");
   $(this).parents(".row").children(".col-1").children('[alt="Добавить"]').css("display","none");
   $(this).parents(".row").children(".col-1").children('[alt="Удалить"]').css("display","none");
   $(this).parents(".row").children(".col-2").children('[alt="Удалить автора"]').css("display","inline-block");
   $(this).parents(".row").children(".col-2").children('[alt="Удалить жанр"]').css("display","inline-block");
   $(this).parents(".row").children(".col-2").children('.add').css("display","inline-block");
   $(this).parents(".row").children(".col-2").children('.add').children('img').css("display","inline-block");
});

$(document).on("click",'[alt="Сохранить"]',function () {
   if($(this).parents("form")[0].checkValidity()) {
      startLoadingAnimation();
      let id = $(this).parents(".row").attr("id");
      let title = {};
      let pubDate = {};
      let genres = {};
      let authors = {};
      let validation = true;
      title[$(this).parents(".row").children('.col-4').children('input').attr('name')] = $(this).parents(".row").children('.col-4').children('input').val();
      pubDate[$(this).parents(".row").children('.col-2').children('[type="number"]').attr('name')] = $(this).parents(".row").children('.col-2').children('[type="number"]').val();
      $(this).parents(".row").children('.col-2').children('.genreInput').each(function (index, value) {
         if($(value).attr('name')===""){
            genres[$(value).val()] = 'new';
            validation = false;
         }else {
            validation = (validation === true) ? ($(value).val() === "DELETE"):false;
            genres[$(value).attr('name')] = $(value).val();
         }
      });
      if(!validation){
         validation = !validation;
         $(this).parents(".row").children('.col-2').children('.authorInput').each(function (index, value) {
            if($(value).attr('name')===""){
               authors[$(value).val()] = 'new';
               validation = false;
            }else {
               validation = (validation === true) ? ($(value).val() === "DELETE"):false;
               authors[$(value).attr('name')] = $(value).val();
            }
         });
         if(!validation){
            $('body').addClass('lockHover');
            $(this).css("display","none");
            $(this).siblings().css("display","");
            $(this).parents(".row").children(".col-4").children("p").css("display","inline-block");
            $(this).parents(".row").children(".col-4").children("input").css("display","none");
            $(this).parents(".row").children(".col-1").children('[alt="Отменить"]').css("display","none");
            $(this).parents(".row").children(".col-1").children('[alt="Добавить"]').css("display","");
            $(this).parents(".row").children(".col-1").children('[alt="Удалить"]').css("display","");
            $(this).parents(".row").children(".col-2").children('[alt="Удалить автора"]').css("display","");
            $(this).parents(".row").children(".col-2").children('[alt="Удалить жанр"]').css("display","");
            $(this).parents(".row").children(".col-2").children('.add').css("display","");
            $(this).parents(".row").children(".col-2").children('.add').children('img').css("display","");
            $(this).parents(".row").children(".col-1").children("p").css("display","inline-block");
            $(this).parents(".row").children(".col-2").children("p").css("display","inline-block");
            $(this).parents(".row").children(".col-1").children("input").css("display","none");
            $(this).parents(".row").children(".col-2").children("input").css("display","none");
            let dataPut = {"id": id, "title": title, "pubDate": pubDate, "genres": genres, "authors": authors};
            console.log(dataPut);
            dataPut = JSON.stringify(dataPut);
            $.ajax({
               context: this,
               url: '/book/',
               type: 'PUT',
               data: {dataPut},
               dataType: 'text',
               success: function () {
                  stopLoadingAnimation();
                  $(bookParse(bookInfo($(this)))).insertAfter($(this).parents('form'));
                  $(this).parents('form').remove();
                  updateAutocomplete();
               },
               error: function (data) {
                  console.log(data);
               },
            });
         }else {
            stopLoadingAnimation();
            warningEmptyAuthorOrGenre();
         }
      }else {
         stopLoadingAnimation();
         warningEmptyAuthorOrGenre();
      }
   }else {
      $(this).parents("form")[0].reportValidity();
   }
});

$(document).on("click",'[alt="Сохранить новую"]',function () {
   if($(this).parents("form")[0].checkValidity()){
      startLoadingAnimation();
      let dataPost = bookInfo($(this));
      if(!dataPost['validation']){
         dataPost = JSON.stringify(dataPost);
         $.ajax({
            url: '/book/',
            type: 'POST',
            data: {dataPost},
            dataType: 'json',
            success: function(response) {
               stopLoadingAnimation();
               dataPost=JSON.parse(dataPost);
               dataPost['id'] = response;
               $(bookParse(dataPost)).insertAfter($('.newBook').parents('form'));
               updateAutocomplete();
            }
         });
         $('.newBook').css('display',"");
         $('body').addClass('lockHover');
      }else {
         stopLoadingAnimation();
         warningEmptyAuthorOrGenre();
      }
   }else {
      $(this).parents("form")[0].reportValidity();
   }
});
$(document).on("click",'[alt="Удалить"]',function () {
   let current_book = $(this).parents(".row");
   $('<div>Удаление книги приводит к потере жанров и авторов, если их нет в других книгах</div>').dialog({
      title: 'Вы действительно хотите удалить книгу?',
      resizable: false,
      modal: true,
      show: 'slide',
      draggable: false,
      buttons: {
         'Отмена': function()  {
            $(this).dialog( 'close' );
         },
         'Удалить': function()  {
            startLoadingAnimation();
            $.ajax({
               url: '/book/'+current_book.attr('id'),
               type: 'DELETE',
               dataType: 'json'
            });
            $(this).dialog( 'close' );
            stopLoadingAnimation();
            current_book.remove();
            updateAutocomplete();
         }
      }
   });
});

$(document).on("click",'[alt="Отменить"]',function () {
   $('body').addClass('lockHover');
   $(this).parents(".row").replaceWith(cancel);
});

$(document).on("click",'[alt="Отменить создание"]',function () {
   $('body').addClass('lockHover');
   $('.newBook').css('display','none');
});

$(document).on("click",'[alt="Добавить книгу"]',function () {
   $('.newBook').css('display','flex');
   $('body').removeClass('lockHover');
});

$(document).on("click",'span:contains("Добавить жанр")',function () {
   $('<img src="/assets/img/Minus.png" alt="Удалить жанр" class="minIcons"> <input class="genreInput autocomplete form-control" type="text" maxlength="100" placeholder="Жанр" style="display: inline-block" name="">').insertBefore($(this));
});

$(document).on("click",'span:contains("Добавить автора")',function () {
   $('<img src="/assets/img/Minus.png" alt="Удалить автора" class="minIcons"> <input class="authorInput autocomplete form-control" type="text" maxlength="100" placeholder="Автор" style="display: inline-block" name="">').insertBefore($(this));
});

$(document).on("click",'[alt="Удалить автора"]', function (){deleteFromDisplay($(this));});

$(document).on("click",'[alt="Удалить жанр"]', function (){deleteFromDisplay($(this));});