from flask import Flask, jsonify, request

app = Flask(__name__)

# Decorator function for enabling CORS
def add_cors_headers(response):
    response.headers['Access-Control-Allow-Origin'] = '*'
    response.headers['Access-Control-Allow-Methods'] = 'GET, POST'
    response.headers['Access-Control-Allow-Headers'] = 'Content-Type'
    return response

@app.route('/api/discountCalculator', methods=['GET'])
def discount_calculator():
    total_order_amount = float(request.args.get('TotalOrderAmount'))

    if total_order_amount >= 10000:
        discount_rate = 0.13
    elif total_order_amount >= 5000:
        discount_rate = 0.06
    elif total_order_amount >= 3000:
        discount_rate = 0.03
    else:
        discount_rate = 0

    discount_amount = total_order_amount * discount_rate
    new_order_amount = total_order_amount - discount_amount

    response_data = {
        "DiscountRate": discount_rate,
        "NewOrderAmount": new_order_amount
    }

    response = jsonify(response_data)
    response = add_cors_headers(response)  # Add CORS headers to the response
    return response

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8080)

