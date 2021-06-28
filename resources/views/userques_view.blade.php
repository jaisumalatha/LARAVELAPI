<link rel="stylesheet" type="text/css" href="http://www.mercurysolutions.co/app/webroot/css/common/bootstrap.min.css"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<div class="questions" id="question-div">
	<form class="chat" id="message" action="#" method="post">
  @csrf
  @foreach ($users as $user)
			<div align="center" id="div-{{$user->ques_id}}" class="question hide">
				<p>Question {{$user->ques_id}} : <input type="hidden" id="question_id{{$user->ques_id}}" name="question_id" value="{{ $user->ques_id }}" id="{{ $user->ques_id }}" />{{ $user->ques_des }}</p>
        <input type="hidden" value="{{Auth::id()}}" name="userid">
      	@if ($user->ques_option != "")
  @foreach(explode(',', $user->ques_option) as $info)
        <label class="radio-inline" data-id="{{$user->ques_id}}" ><input type="radio" required name="answere_val" value="{{$info}}">{{$info}}</label>
  @endforeach	
  @endif
    	</div>
      @endforeach	
      <button type="submit" class="button hide" id="next">Submit</button>
			<div class="button hide" id="prev">Prev</div>
    </form>
	</div>	
<style>
.hide{
display:none;
}
</style>
<script>


var element = document.getElementById("div-1");
  element.classList.remove("hide");

	var maxq = 5;
    	$('.radio-inline').click(function(e) {
            var id = parseInt($(this).data('id'));
			if(id==1) $('.button').addClass('hide');

			let btnSend = document.getElementById("#question_id");
			if (!btnSend) {
					for (i = 0; i < 5; i++) {
				$('#question_id'+i).attr( 'value',id);
				}
			}

			if(id!=(maxq-1)){$('#next').removeClass('hide');
			}
			var next = (id+1);
			var prev = (id-1);
			if(id!=(maxq-1)) {$('#submit,#prev').removeClass('hide');
 }
			$('#next').data('id',next);
			$('#prev').data('id',prev);
		});
		$('#next').click(function(e) {
			var id = $(this).data('id');
			$('.button').addClass('hide');
			//$('#next').removeClass('hide');
			if(id!=(maxq-1)) {$('#submit,#prev').removeClass('hide');
 }
			else {$('.button').addClass('hide');$('#prev').removeClass('hide');}
			$('.question').addClass('hide');
			$('#div-'+id).removeClass('hide');
			var next = id+1;
			var prev = id-1;
			$('#next').data('id',next);
			$('#prev').data('id',prev);
		});
		$('#prev').click(function(e) {
			var id = $(this).data('id');
			$('#prev').removeClass('hide');
			if(id==1)$('.button').addClass('hide');
			$('.question').addClass('hide');
			$('#div-'+id).removeClass('hide');
			var next = id+1;
			var prev = id-1;
			$('#next').data('id',next);
			$('#prev').data('id',prev);
		});

		$('#message').submit(function(e) {
    e.preventDefault();
    $.ajax({
        type: "post",
        url: '/store-form',
        data: $("#message").serialize(),
        success: function(store) {

        },
        error: function() {
        }
    });
});

		
</script>
