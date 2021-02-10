<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DocumentController extends Controller
{

    public function uploadFile()
    {
        return view('upload');
    }

    public function uploadFileAction($file)
    {
        $response = Http::attach(
            'file',
            file_get_contents($file),
            'contrato.pdf'
        )
            ->withHeaders([
                "X-Api-Key" => "Previsa|11531a9b36f8b0439909fc571f0201a764fbed5cf1a2616e13f49565ab17cdd9"
            ])
            ->post('https://assinador.previsa.com.br/api/uploads', [
                'file' => $file
            ]);

        if (!$response->successful()) {
            $response->throw();
        } else {
            return $response['id'];
        }
    }

    public function docUpload($id)
    {
        $response = Http::withHeaders([
            "X-Api-Key"     => "Previsa|11531a9b36f8b0439909fc571f0201a764fbed5cf1a2616e13f49565ab17cdd9",
            "accept" => "text/plain",
            "Content-Type" => "application/json"
        ])
            ->post(
                'https://assinador.previsa.com.br/api/documents',
                [
                    "files" => [
                        [
                            "displayName" => "Contrato Swagger",
                            "id" => "$id",
                            "name" => "Contrato.pdf",
                            "contentType" => "application/pdf"
                        ]
                    ],
                    "flowActions" => [
                        [
                            "type" => "Signer",
                            "step" => 1,
                            "user" => [
                                "name" => "John Wick",
                                "identifier" => "05976325610",
                                "email" => "stephan@previsa.com.br"
                            ]
                        ]
                    ]
                ]
            );

        if (!$response->successful()) {
            $response->throw();
        } else {
            print_r(json_decode($response));
        }
    }

    public function save(Request $req)
    {
        $file = $req->file('arquivo');
        $id = $this->uploadFileAction($file);
        $this->docUpload($id);
    }
}
