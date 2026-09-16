import React from 'react';
import Input from '../ui/Input';
import Field from '../ui/Field';

export default function CustomerFields({ value, onChange, errors = {} }) {
    const set = (key) => (e) => onChange({ ...value, [key]: e.target.value });

    return (
        <div className="grid gap-4 sm:grid-cols-2">
            <Field label="First name" error={errors.first_name}>
                <Input
                    type="text"
                    value={value.first_name || ''}
                    onChange={set('first_name')}
                    invalid={!!errors.first_name}
                    required
                    autoFocus
                />
            </Field>
            <Field label="Last name" error={errors.last_name}>
                <Input
                    type="text"
                    value={value.last_name || ''}
                    onChange={set('last_name')}
                    invalid={!!errors.last_name}
                />
            </Field>
            <Field label="Phone" error={errors.phone}>
                <Input
                    type="tel"
                    value={value.phone || ''}
                    onChange={set('phone')}
                    placeholder="5550001234"
                    invalid={!!errors.phone}
                    required
                />
            </Field>
            <Field label="Email" error={errors.email}>
                <Input
                    type="email"
                    value={value.email || ''}
                    onChange={set('email')}
                    invalid={!!errors.email}
                />
            </Field>
            <Field label="Company" error={errors.company}>
                <Input
                    type="text"
                    value={value.company || ''}
                    onChange={set('company')}
                    invalid={!!errors.company}
                />
            </Field>
            <Field label="Address" error={errors.address}>
                <Input
                    type="text"
                    value={value.address || ''}
                    onChange={set('address')}
                    invalid={!!errors.address}
                />
            </Field>
            <Field label="City" error={errors.city}>
                <Input
                    type="text"
                    value={value.city || ''}
                    onChange={set('city')}
                    invalid={!!errors.city}
                />
            </Field>
            <Field label="State" error={errors.state} hint="Two-letter code, e.g. CA">
                <Input
                    type="text"
                    value={value.state || ''}
                    onChange={set('state')}
                    maxLength={2}
                    invalid={!!errors.state}
                />
            </Field>
            <Field label="Zip" error={errors.zip}>
                <Input
                    type="text"
                    value={value.zip || ''}
                    onChange={set('zip')}
                    maxLength={5}
                    invalid={!!errors.zip}
                />
            </Field>
        </div>
    );
}