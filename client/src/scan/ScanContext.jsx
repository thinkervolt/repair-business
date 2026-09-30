import React, { createContext, useCallback, useContext, useMemo, useRef } from 'react';

const ScanContext = createContext({
    setScanHandler: () => {},
    hasScanHandler: () => false,
    dispatchScan: () => false,
});

export function ScanProvider({ children }) {
    const handlerRef = useRef(null);

    const setScanHandler = useCallback((fn) => {
        handlerRef.current = typeof fn === 'function' ? fn : null;
    }, []);

    const hasScanHandler = useCallback(() => handlerRef.current !== null, []);

    const dispatchScan = useCallback((code) => {
        const handler = handlerRef.current;
        if (!handler) return false;
        handler(code);
        return true;
    }, []);

    const value = useMemo(
        () => ({ setScanHandler, hasScanHandler, dispatchScan }),
        [setScanHandler, hasScanHandler, dispatchScan]
    );

    return <ScanContext.Provider value={value}>{children}</ScanContext.Provider>;
}

export function useScan() {
    return useContext(ScanContext);
}
