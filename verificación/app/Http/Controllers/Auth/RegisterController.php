<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    
    
    use RegistersUsers {
        register as registration;
    }

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */


     // Agrega tu método de encriptación al controlador
    private function encriptar($mensaje)
    {
            $diccionario = [
                "A" => "YC", "B" => "ZD" ,"C" => "AC" ,"D" => "BF" ,"E" => "CG",
                "F" => "DH", "G" => "EI" ,"H" => "FJ" ,"I" => "GK" ,"J" => "HL",                            
                "K" => "IM", "L" => "JN" ,"M" => "KÑ" ,"N" => "LO" ,"Ñ" => "MP", 
                "O" => "NQ", "P" => "ÑR" ,"Q" => "OS" ,"R" => "PT" ,"S" => "QU",                            
                "T" => "RV", "U" => "SW" ,"V" => "TX" ,"W" => "UY" ,"X" => "VZ",
                "Y" => "WA", "Z" => "XB", 

                "a" => "yc", "b" => "zd" ,"c" => "ac" ,"d" => "bf" ,"e" => "cg",
                "f" => "dh", "g" => "ei" ,"h" => "fj" ,"i" => "gk" ,"j" => "hl",                            
                "k" => "im", "l" => "jn" ,"m" => "kñ" ,"n" => "lo" ,"ñ" => "mp", 
                "o" => "nq", "p" => "ñr" ,"q" => "os" ,"r" => "pt" ,"s" => "qu",                            
                "t" => "rv", "u" => "sw" ,"v" => "tx" ,"w" => "uy" ,"x" => "vz",
                "y" => "wa", "z" => "xb", 
                
                "1" => "93", "2" => "04" ,"3" => "15" ,"4" => "26" ,"5" => "37", 
                "6" => "48", "7" => "59" ,"8" => "60" ,"9" => "71" ,"0" => "82", 
                " " => " "  
                // Agrega más reemplazos según tu diccionario
            ];

            $mensajeEncriptado = '';

        // Iterar sobre cada carácter del mensaje
        for ($i = 0; $i < strlen($mensaje); $i++) {
            $caracter = $mensaje[$i];

            // Verificar si el carácter está en el diccionario
            if (array_key_exists($caracter, $diccionario)) {
                // Agregar el valor encriptado al mensaje encriptado
                $mensajeEncriptado .= $diccionario[$caracter];
            } else {
                // Si el carácter no está en el diccionario, agregarlo sin cambios
                $mensajeEncriptado .= $caracter;
            }
        }

        return $mensajeEncriptado;
    }



    protected function create(array $data)
    {
        $encryptedPassword = $this->encriptar($data['password']);
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $encryptedPassword,
            'google2fa_secret' => $data['google2fa_secret'],
        ]);
    }


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
  
        $google2fa = app('pragmarx.google2fa');
  
        $registration_data = $request->all();
  
        $registration_data["google2fa_secret"] = $google2fa->generateSecretKey();
  
        $request->session()->put('registration_data', $registration_data);
  
        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $registration_data['email'],
            $registration_data['google2fa_secret']
        );
          
        return view('google2fa.register', ['QR_Image' => $QR_Image, 'secret' => $registration_data['google2fa_secret']]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function completeRegistration(Request $request)
    {        
        $request->merge(session('registration_data'));
  
        return $this->registration($request);
    }


}
