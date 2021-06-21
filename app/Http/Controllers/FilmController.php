<?php

namespace App\Http\Controllers;

use App\Events\FilmCreated;
use App\Http\Resources\FilmCollection;
use App\Models\Film;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Resources\Film as FilmResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return FilmCollection
     */
    public function index(): FilmCollection
    {
        return new FilmCollection(Film::paginate(1));
    }

    /**
     * Get a validator for an incoming film creation request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'release_date' => ['required', 'date'],
            'rating' => ['required', 'digits_between:1,5'],
            'price' => ['required', 'numeric', 'min:1'],
            'country' => ['required', 'exists:countries,id'],
            'photo' => ['required', 'image'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Film
     */
    protected function createFilm(array $data): Film
    {
        return Film::create([
            'country_id' => $data['country'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name'], '-'),
            'description' => $data['description'],
            'release_date' => Carbon::parse($data['release_date'])->format('yy-m-d'),
            'rating' => $data['rating'],
            'price' => $data['price'],
            'photo' => $data['photo_path'] ?? '',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return FilmResource
     * @throws ValidationException
     */
    public function store(Request $request): FilmResource
    {
        $this->validator($request->all())->validate();

        if ($request->file('photo')->isValid()) {
            $path = $request->file('photo')->store('photos');
            $request->request->add(['photo_path' => $path]);
        }

        event(new FilmCreated($film = $this->createFilm($request->all())));

        return new FilmResource($film);
    }

    /**
     * Display the specified resource.
     *
     * @param $slug
     * @return FilmResource
     */
    public function show($slug): FilmResource
    {
        $item = Film::where('slug', $slug)->first();
        return new FilmResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        try {
            Film::destroy($id);
        } catch (Exception $e) {
            return new Response('', 204);
        }
        return new Response('', 204);
    }
}
