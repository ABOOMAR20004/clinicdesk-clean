<?php

declare(strict_types=1);

class DashboardController
{
    public function index(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        $role = Auth::role();
        $appointments = new AppointmentModel();

        if ($role === 'admin') {
            view('dashboard/admin', [
                'pageTitle' => 'Admin Dashboard',
                'roleTotals' => (new UserModel())->countByRole(),
                'todayTotal' => $appointments->countToday(),
                'weekStatus' => $appointments->countThisWeekByStatus(),
                'recent' => $appointments->getRecent(5),
                'chartRows' => $appointments->chartLast14Days(),
            ]);
            return;
        }

        if ($role === 'doctor') {
            $doctor = (new DoctorModel())->findByUserId(Auth::id());
            if (!$doctor) {
                flash('warning', 'Your doctor profile is not complete. Contact admin.');
                view('dashboard/doctor', ['pageTitle' => 'Doctor Dashboard', 'doctor' => null]);
                return;
            }

            view('dashboard/doctor', [
                'pageTitle' => 'Doctor Dashboard',
                'doctor' => $doctor,
                'today' => $appointments->getTodayByDoctor((int) $doctor['id']),
                'monthly' => $appointments->doctorMonthlyCounts((int) $doctor['id']),
                'upcoming' => $appointments->getUpcomingByDoctor((int) $doctor['id'], 5),
            ]);
            return;
        }

        view('dashboard/patient', [
            'pageTitle' => 'Patient Dashboard',
            'stats' => $appointments->patientStats(Auth::id()),
            'prescriptionCount' => (new PrescriptionModel())->countByPatient(Auth::id()),
        ]);
    }
}
