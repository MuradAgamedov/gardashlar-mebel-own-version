<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use App\Repositories\CertificateRepository;
use App\Repositories\LanguageRepository;
use App\Http\Requests\Admin\Certificate\StoreCertificateRequest;
use App\Http\Requests\Admin\Certificate\UpdateCertificateRequest;

class CertificateController extends Controller
{
    public function __construct(
        public CertificateRepository $repository,
        public LanguageRepository $languageRepository
    )
    {
    }

    public function index()
    {
        $certificates = $this->repository->getCertificates();
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.certificates.index', compact('certificates', 'languages'));
    }

    public function create()
    {
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.certificates.create', compact('languages'));
    }

    public function store(StoreCertificateRequest $request)
    {
        try {
            $this->repository->createCertificate(
                [
                    'date' => $request->input('date'),
                    'status' => $request->input('status', true),
                ],
                $request->input('translations'),
                $request->file('image')
            );

            return redirect()->route('admin.certificates.index')
                ->with('success', 'Certificate created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating certificate: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Certificate $certificate)
    {
        $certificate->load('translates.language');
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.certificates.edit', compact('certificate', 'languages'));
    }

    public function update(UpdateCertificateRequest $request, Certificate $certificate)
    {
        try {
            $this->repository->updateCertificate(
                $certificate,
                [
                    'date' => $request->input('date'),
                    'status' => $request->input('status', true),
                ],
                $request->input('translations'),
                $request->file('image')
            );

            return redirect()->route('admin.certificates.index')
                ->with('success', 'Certificate updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating certificate: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Certificate $certificate)
    {
        try {
            $this->repository->deleteCertificate($certificate);
            return redirect()->route('admin.certificates.index')
                ->with('success', 'Certificate deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting certificate: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'certificates' => 'required|array',
            'certificates.*' => 'required|integer|exists:certificates,id',
        ]);

        try {
            $this->repository->reorderCertificates($request->certificates);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
