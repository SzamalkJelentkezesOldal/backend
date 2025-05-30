<?php

namespace App\Http\Controllers;

use App\Http\Requests\JelentkezoRequest;
use App\Mail\JelentkezoMail;
use App\Mail\PortfolioMegerositesMail;
use App\Models\Jelentkezes;
use App\Models\Jelentkezo;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JelentkezoController extends Controller
{
    public function postJelentkezoJelentkezesPortfolio(JelentkezoRequest $request)
    {
        $validation = Validator::make($request->all(), $request->rules());

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors()
            ], 422);
        }


        try {
            $token = Str::random(40);

            // jelentkezobe
            $jelentkezo = new Jelentkezo();
            $jelentkezo->nev = $request->jelentkezo['nev'];
            $jelentkezo->email = $request->jelentkezo['email'];
            $jelentkezo->tel = $request->jelentkezo['tel'];
            $jelentkezo->token = $token;
            $jelentkezo->save();

            $regisztraciosLink = url("http://localhost:3000/register/{$token}");


            //jelentkezesbe
            foreach ($request->jelentkezes['kivalasztottSzakok'] as $index => $szakId) {
                $jelentkezes = new Jelentkezes();
                $jelentkezes->szak_id = $szakId;
                $jelentkezes->jelentkezo_id = $jelentkezo->id;
                $jelentkezes->allapot = 1; 
                $jelentkezes->sorrend = $index; 

                $jelentkezes->save();
            }

            // portfolioba
            if (!empty($request->portfolio['portfolioSzakok'])) {
                foreach ($request->portfolio['portfolioSzakok'] as $portfolioSzak) {
                    $portfolio = new Portfolio();
                    $portfolio->jelentkezo_id = $jelentkezo->id;
                    $portfolio->portfolio_url = $portfolioSzak['portfolio_url'];
                    $portfolio->szak_id = $portfolioSzak['szak_id'];
                    $portfolio->allapot = 'Eldöntésre vár';
                    $portfolio->save();
                }
                Mail::to($request->jelentkezo['email'])->send(new PortfolioMegerositesMail($request->jelentkezo['nev']));

            } else {
                Mail::to($request->jelentkezo['email'])->send(new JelentkezoMail($request->jelentkezo['nev'], $regisztraciosLink));
            }

            return response()->json([
                'message' => 'Sikeres jelentkezés!',
                'data' => $jelentkezo
            ], 201);
        } catch (\Exception $e) {
            Log::error('Hiba történt: ' . $e->getMessage());
            return response()->json([
                'error' => 'Belső hiba történt: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function index(Request $request)
    {
        $page   = $request->input('page', 1);
        $limit  = $request->input('limit', 10);
        $filter = $request->input('filter', 1); // 1 = Összes jelentkező, 2 = Csak jelentkezett, 3 = Beiratkozás alatt
        $search = $request->input('search', '');
        $searchField = $request->input('searchField', ''); 

        $query = Jelentkezo::query()
            ->select('id', 'nev', 'tel', 'email', 'created_at')
            ->with([
                'user:id,email,created_at',
                'jelentkezesek' => function($q) {
                    $q->select('id', 'jelentkezo_id', 'allapot', 'sorrend', 'szak_id', 'updated_at', 'lezart')
                    ->orderBy('sorrend', 'asc')
                    ->with(['allapotszotar', 'szak']);
                },
                'torzsadatok',
                'dokumentumok',
                'portfolios'
            ]);

        if (!empty($search)) {
            if (!empty($searchField) && in_array($searchField, ['nev', 'email'])) {
                $query->where($searchField, 'like', "%{$search}%");
            } else {
                $query->where(function($q) use ($search) {
                    $q->where('nev', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            }
        }

        if ($filter == 2) {
            $query->whereNotIn('email', function($q) {
                $q->select('email')->from('users');
            });
        } elseif ($filter == 3) {
            $query->whereIn('email', function($q) {
                $q->select('email')->from('users');
            });
        }

        $totalCount = $query->count();
        $applicants = $query->skip(($page - 1) * $limit)->take($limit)->get();

        if ($filter == 1) {
            $userEmails = DB::table('users')->pluck('email')->toArray();
        } else {
            $userEmails = ($filter == 3) ? $applicants->pluck('email')->toArray() : [];
        }

        $results = $applicants->map(function ($applicant) use ($filter, $userEmails) {
            $statuses = $applicant->jelentkezesek->map(function($j) {
                return optional($j->allapotszotar)->elnevezes;
            })->toArray();

            if ($filter == 2) {
                $overallStatus = 'Jelentkezett';
            } elseif ($filter == 1) {
                if (!in_array($applicant->email, $userEmails)) {
                    $overallStatus = 'Jelentkezett';
                } else {
                    $overallStatus = $this->osszefoglaltStatusz($statuses);
                }
            } elseif ($filter == 3) {
                $overallStatus = $this->osszefoglaltStatusz($statuses);
            }

            $torzsadatok = in_array($applicant->email, $userEmails) ? $applicant->torzsadatok : null;

            $dokumentumok = in_array($applicant->email, $userEmails) ? $applicant->dokumentumok->map(function($doc) {
                $files = $doc->fajlok;
                if (!is_array($files)) {
                    $files = json_decode($files, true) ?: [];
                }
                $previewUrls = array_map(function($file) {
                    return route('dokumentum.preview', ['path' => $file]);
                }, $files);

                return [
                    'id' => $doc->id,
                    'dokumentumTipus' => $doc->tipus ? $doc->tipus->elnevezes : null,
                    'fajlok' => $files,
                    'previewUrls' => $previewUrls,
                    'created_at' => $doc->created_at,
                ];
            })->toArray() : null;

            $jelentkezesek = $applicant->jelentkezesek->map(function($j) {
                return [
                    'id' => $j->id,
                    'sorrend' => $j->sorrend,
                    'updated_at' => $j->updated_at,
                    'allapotszotar' => $j->allapotszotar,
                    'allapot' => $j->allapot,
                    'jelentkezo_id' => $j->jelentkezo_id,
                    'szak_id' => $j->szak->id,
                    'szak' => $j->szak->elnevezes,
                    'tagozat' => $j->szak->nappali,
                    'lezart' => $j->lezart,
                ];
            });

            $portfoliok = $applicant->portfolios && $applicant->portfolios->count()
                ? $applicant->portfolios->map(function($pf) {
                    return [
                        'id' => $pf->id,
                        'szak' => $pf->szak->elnevezes,
                        'portfolio_url' => $pf->portfolio_url,
                        'allapot' => $pf->allapot,
                        'ertesito' => $pf->ertesito,
                        'created_at' => $pf->created_at,
                        'tagozat' => $pf->szak ? (int)$pf->szak->nappali : null,
                    ];
                })->toArray()
                : null;

            return [
                'id' => $applicant->id,
                'nev' => $applicant->nev,
                'email' => $applicant->email,
                'tel' => $applicant->tel,
                'beregisztralt' => $applicant->user ? $applicant->user->created_at : null,
                'jelentkezett' => $applicant->created_at,
                'status' => $overallStatus,
                'jelentkezesek' => $jelentkezesek,
                'torzsadatok' => $torzsadatok,
                'dokumentumok' => $dokumentumok,
                'portfolioAllapot' => $this->osszefoglaltPortfolioAllapot($portfoliok),
                'portfoliok' => $portfoliok,
            ];
        });

        return response()->json([
            'results'    => $results,
            'totalCount' => $totalCount,
        ]);
    }


    private function osszefoglaltPortfolioAllapot($portfoliok) {
        // Ha nincs portfólió, akkor üres értéket adunk vissza.
        if (empty($portfoliok) || !is_array($portfoliok) || count($portfoliok) === 0) {
            return "";
        }
        
        // Kinyerjük az összes portfólió állapotát
        $statuses = array_map(function($pf) {
            return $pf['allapot'];
        }, $portfoliok);
        
        // Ha bármelyik portfólió "Eldöntésre vár" állapotban van, az összesített állapot legyen "Eldöntésre vár"
        if (in_array("Eldöntésre vár", $statuses)) {
            return "Eldöntésre vár";
        }
        
        // Ha nincs "Eldöntésre vár", és van legalább egy "Elfogadva", akkor az összesített állapot legyen "Elfogadva"
        if (in_array("Elfogadva", $statuses)) {
            return "Elfogadva";
        }
        
        // Ha nincs pending és az összes portfólió "Elutasítva" (vagy legalább egyik sem "Elfogadva"), akkor az összesített állapot legyen "Elutasítva"
        $allRejected = true;
        foreach ($statuses as $status) {
            if ($status !== "Elutasítva") {
                $allRejected = false;
                break;
            }
        }
        if ($allRejected) {
            return "Elutasítva";
        }
        
        // Egyéb esetben az állapot legyen "Eldöntésre vár"
        return "Eldöntésre vár";
    }
    

    /**
     * Az összefoglalt státusz kiszámítása a következő szabályok szerint:
     *
     * - Ha valamelyik szakján "Módosításra vár" van, akkor: "Módosításra vár"
     * - Ha mindegyik szakja "Elutasítva" (és legalább egy jelentkezés van), akkor: "Elutasítva"
     * - Ha van legalább egy "Elfogadva", akkor: "Elfogadva"
     * - Ha az összes szakja "Eldöntésre vár", akkor: "Eldöntésre vár"
     * - Egyéb esetben: "Folyamatban"
     */
    private function osszefoglaltStatusz($statuses)
    {
        if (in_array('Módosításra vár', $statuses)) {
            return 'Módosításra vár';
        }
        if (!empty($statuses) && array_reduce($statuses, function($carry, $status) {
            return $carry && ($status === 'Elutasítva');
        }, true)) {
            return 'Elutasítva';
        }
        if (in_array('Elfogadva', $statuses)) {
            return 'Elfogadva';
        }
        if (!empty($statuses) && array_reduce($statuses, function($carry, $status) {
            return $carry && ($status === 'Eldöntésre vár');
        }, true)) {
            return 'Eldöntésre vár';
        }
        return 'Folyamatban';
    }

    public function nappaliJelentkezok()
    {
        // Lekérdezés a nappali szakokra jelentkezettekről
        
        $jelentkezok = DB::table('jelentkezos')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('jelentkezes')
                    ->join('szaks', 'jelentkezes.szak_id', '=', 'szaks.id')
                    ->whereColumn('jelentkezes.jelentkezo_id', 'jelentkezos.id')
                    ->where('szaks.nappali', '=', 0); // Ha van nem-nappali jelentkezés, kizárjuk
            })
            ->select('jelentkezos.nev', 'jelentkezos.email')
            ->distinct()
            ->get();

        return $jelentkezok;
    }
    public function estiJelentkezok()
    {
        // Lekérdezés a nappali szakokra jelentkezettekről
        
        $jelentkezok = DB::table('jelentkezos')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('jelentkezes')
                    ->join('szaks', 'jelentkezes.szak_id', '=', 'szaks.id')
                    ->whereColumn('jelentkezes.jelentkezo_id', 'jelentkezos.id')
                    ->where('szaks.nappali', '=', 1); // Ha van nem-nappali jelentkezés, kizárjuk
            })
            ->select('jelentkezos.nev', 'jelentkezos.email')
            ->distinct()
            ->get();

        return $jelentkezok;
    }
    public function csakEgyTagozatraJelentkezett(int $szam)
    {
        // Lekérdezés a nappali szakokra jelentkezettekről
        
        $jelentkezok = DB::table('jelentkezos')
            ->whereNotExists(function ($query) use ($szam) {
                $query->select(DB::raw(1))
                    ->from('jelentkezes')
                    ->join('szaks', 'jelentkezes.szak_id', '=', 'szaks.id')
                    ->whereColumn('jelentkezes.jelentkezo_id', 'jelentkezos.id')
                    ->where('szaks.nappali', '=', $szam); // Ha van nem-nappali jelentkezés, kizárjuk
            })
            ->select('jelentkezos.nev', 'jelentkezos.email')
            ->distinct()
            ->get();

        return $jelentkezok;
    }
}
