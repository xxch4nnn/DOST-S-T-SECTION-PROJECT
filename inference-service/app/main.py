from fastapi import FastAPI
from pydantic import BaseModel

app = FastAPI(title="DOSTorage Inference Service")


class PredictRequest(BaseModel):
    prompt_id: str
    version: str | None = None
    input: dict


class PredictResponse(BaseModel):
    prompt_id: str
    version: str
    output: dict
    latency_ms: float


@app.get("/health")
async def health() -> dict:
    return {"status": "ok", "service": "inference"}


@app.post("/predict", response_model=PredictResponse)
async def predict(request: PredictRequest) -> PredictResponse:
    # Placeholder implementation
    return PredictResponse(
        prompt_id=request.prompt_id,
        version=request.version or "unknown",
        output={"label": "low", "confidence": 0.5},
        latency_ms=0.0,
    )
