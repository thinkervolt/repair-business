import React from 'react';
import { Wrench } from 'lucide-react';
import Card from '../components/ui/Card';

export default function Placeholder({ title }) {
    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">{title}</h1>
                <p className="mt-1 text-sm text-slate-500">
                    This section of the app is under construction.
                </p>
            </div>
            <Card>
                <div className="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <span className="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                        <Wrench className="h-6 w-6" />
                    </span>
                    <p className="mt-4 max-w-sm text-sm text-slate-500">
                        The <strong className="font-semibold text-slate-700">{title}</strong>{' '}
                        module is coming soon. This placeholder will be replaced by the next
                        milestone.
                    </p>
                </div>
            </Card>
        </div>
    );
}