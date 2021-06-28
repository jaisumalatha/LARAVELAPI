<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <link rel="stylesheet" type="text/css" href="http://www.mercurysolutions.co/app/webroot/css/common/bootstrap.min.css"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<div class="questions" id="question-div">
  <form name="" id="question-form" method="post" action="{{url('store-form')}}">
  @csrf
  @foreach ($users as $user)
			<div align="center" id="div-{{$user->ques_id}}" class="question hide">
				<p>Question {{$user->ques_id}} : <input type="hidden" name="question_id" value="{{ $user->ques_id }}" id="{{ $user->ques_id }}" />{{ $user->ques_des }}</p>
        <input type="hidden" value="{{Session::getId()}}" name="userid">
      	@if ($user->ques_option != "")
  @foreach(explode(',', $user->ques_option) as $info)
        <label class="radio-inline" data-id="{{$user->ques_id}}" ><input type="radio" required name="answere_val" value="{{$info}}">{{$info}}</label>
  @endforeach	
  @endif
    	</div>
      @endforeach	
      <div class="button hide" id="next">Next</div>
			<div class="button hide" id="prev">Prev</div>
			<button type="submit" id="submit" class="btn btn-primary btn-sm pull-right  hide">Submit</button>
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
	var maxq = 4;
    	$('.radio-inline').click(function(e) {
            var id = parseInt($(this).data('id'));
			if(id==1) $('.button').addClass('hide');
			if(id!=(maxq-1)){$('#next').removeClass('hide');}
			var next = (id+1);
			var prev = (id-1);
			$('#next').data('id',next);
			$('#prev').data('id',prev);
		});
		$('#next').click(function(e) {
			var id = $(this).data('id');
			$('.button').addClass('hide');
			//$('#next').removeClass('hide');
			if(id==(maxq-1)) {$('#submit,#prev').removeClass('hide');}
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
</script>

</x-app-layout>
