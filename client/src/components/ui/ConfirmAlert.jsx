import React, { useCallback, useRef, useState } from 'react';
import Alert from './Alert';
import Button from './Button';

export function ConfirmAlert({
    open,
    title,
    message,
    confirmLabel,
    cancelLabel,
    tone = 'error',
    onConfirm,
    onCancel,
}) {
    if (!open) return null;

    return (
        <Alert tone={tone}>
            <div className="flex flex-wrap items-center gap-x-3 gap-y-2">
                <div className="min-w-0 flex-1">
                    <strong>{title}</strong>{' '}
                    <span>{message}</span>
                </div>
                <div className="flex shrink-0 gap-2">
                    <Button type="button" variant="secondary" size="sm" onClick={onCancel}>
                        {cancelLabel}
                    </Button>
                    <Button
                        type="button"
                        size="sm"
                        variant={tone === 'warning' ? 'primary' : 'danger'}
                        onClick={onConfirm}
                    >
                        {confirmLabel}
                    </Button>
                </div>
            </div>
        </Alert>
    );
}

export function useConfirm() {
    const [request, setRequest] = useState(null);
    const resolverRef = useRef(null);

    const confirm = useCallback((options) => {
        setRequest(options);
        return new Promise((resolve) => {
            resolverRef.current = resolve;
        });
    }, []);

    const settle = useCallback((result) => {
        const resolve = resolverRef.current;
        resolverRef.current = null;
        setRequest(null);
        if (resolve) resolve(result);
    }, []);

    return {
        confirm,
        confirmElement: (
            <ConfirmAlert
                open={Boolean(request)}
                title={request ? request.title : ''}
                message={request ? request.message : ''}
                confirmLabel={request ? request.confirmLabel : ''}
                cancelLabel={request ? request.cancelLabel : ''}
                tone={request ? request.tone : 'error'}
                onConfirm={() => settle(true)}
                onCancel={() => settle(false)}
            />
        ),
    };
}
