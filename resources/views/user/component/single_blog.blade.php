@if ($article != null)

  @if($article->isFromTwitter())
    {{-- Tampilan detail untuk artikel Twitter --}}
    <div class="section-header mt-3">
      
      <div class="mb-3">
        <div class="text-dark" style="font-size: 40px; letter-spacing: .5px; line-height: 1.3;">
          {{$article->title}}
        </div>
        <div class="mt-1">
          <small class="font-italic">Created At : {{date('d M Y', strtotime($article->created_at))}} |</small>
          @foreach($article->categories as $value)
              <a class="d-inline underline" href="{{route('blog', ['c' =>$value->name])}}">
                  <small class="font-italic">
                    {{$value->name}},
                  </small>
              </a>
          @endforeach
        </div>
      </div>

      {{-- Twitter Article Component --}}
      @include('partials.twitter-article', ['article' => $article])
      
      {{-- Info tambahan untuk single view --}}
      <div class="mt-4 p-3 bg-light rounded">
        <h5><i class="fab fa-twitter text-primary"></i> Informasi Tweet</h5>
        <p><strong>Dipost di Twitter:</strong> {{ date('d M Y, H:i', strtotime($article->created_at)) }}</p>
        <a href="{{ $article->twitter_url }}" target="_blank" class="btn btn-primary btn-sm">
          <i class="fab fa-twitter"></i> Lihat Tweet Asli
        </a>
      </div>

    </div>
  @else
    {{-- Tampilan normal untuk artikel biasa --}}
    <div class="section-header mt-3">

      <div class="mb-3">

        <div class="text-dark" style="font-size: 40px; letter-spacing: .5px; line-height: 1.3;">

          {{$article->title}}

        </div>

        <div class="mt-1">

          <small class="font-italic">Created At : {{date('d M Y', strtotime($article->created_at))}} |</small>

          @foreach($article->categories as $value)

              <a class="d-inline underline" href="{{route('blog', ['c' =>$value->name])}}">

                  <small class="font-italic">

                    {{$value->name}},

                  </small>

              </a>

          @endforeach

        </div>

      </div>

      <p class="mb-3 article text-justify"> 

        {!! $article->content !!}

      </p>

    </div>
  @endif

@else

  <style>

    .page {

        color: #636b6f;

        font-family: 'Nunito', sans-serif;

        font-weight: 100;

        height: 100vh;

    }

  </style>

  <div class="full-height bg-white mt-5 d-flex align-items-center justify-content-center" style="height: 10vh;">

    <div class="code font-weight-bold text-center" style="border-right: 3px solid; font-size: 60px; padding: 0 15px 0 15px;">

      404

    </div>

    <div class="text-center" style="padding: 10px; font-size: 46px;">

      Not Found

    </div>

  </div>    

@endif